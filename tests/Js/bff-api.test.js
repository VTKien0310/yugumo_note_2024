import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { BffApiError, BffResult, bffGet, bffPut, request } from '../../resources/js/services/bff-api.js';

const URL = 'http://localhost/bff/notes/01TEST';

function jsonResponse(status, payload) {
    return {
        ok: status >= 200 && status < 300,
        status: status,
        json: async () => payload,
    };
}

function stubLocationAssign() {
    const assign = vi.fn();

    Object.defineProperty(window, 'location', {
        configurable: true,
        value: { ...window.location, assign: assign },
    });

    return assign;
}

describe('bff-api', () => {
    beforeEach(() => {
        document.head.innerHTML = '<meta name="csrf-token" content="test-token">';
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
        document.head.innerHTML = '';
    });

    it('injects JSON and CSRF headers, read lazily per call', async () => {
        const fetchMock = vi.fn(async () => jsonResponse(200, { data: null }));
        vi.stubGlobal('fetch', fetchMock);

        await bffGet(URL);

        const [url, options] = fetchMock.mock.calls[0];
        expect(url).toBe(URL);
        expect(options.headers['Accept']).toBe('application/json');
        expect(options.headers['Content-Type']).toBe('application/json');
        expect(options.headers['X-CSRF-TOKEN']).toBe('test-token');

        // Token is re-read on every call, so a morphed meta tag takes effect.
        document.querySelector('meta[name=csrf-token]').content = 'rotated-token';
        await bffGet(URL);
        expect(fetchMock.mock.calls[1][1].headers['X-CSRF-TOKEN']).toBe('rotated-token');
    });

    it('sends no body for GET and serializes the body as JSON for PUT', async () => {
        const fetchMock = vi.fn(async () => jsonResponse(200, { data: null }));
        vi.stubGlobal('fetch', fetchMock);

        await bffGet(URL);
        expect(fetchMock.mock.calls[0][1].body).toBeUndefined();

        await bffPut(URL, { title: 'A title' });
        expect(JSON.parse(fetchMock.mock.calls[1][1].body)).toEqual({ title: 'A title' });
    });

    it('forwards the keepalive option to fetch', async () => {
        const fetchMock = vi.fn(async () => jsonResponse(200, { data: null }));
        vi.stubGlobal('fetch', fetchMock);

        await bffPut(URL, {}, { keepalive: true });

        expect(fetchMock.mock.calls[0][1].keepalive).toBe(true);
    });

    it('unwraps the data payload on success', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => jsonResponse(200, {
            status: 200,
            success: true,
            data: { saved_at: '2026-10-05T00:00:00+00:00' },
        })));

        const result = await bffGet(URL);

        expect(result.ok).toBe(true);
        expect(result.error).toBeNull();
        expect(result.data).toEqual({ saved_at: '2026-10-05T00:00:00+00:00' });
    });

    it('tolerates a missing or unparseable payload on success', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => ({
            ok: true,
            status: 204,
            json: async () => { throw new SyntaxError('Unexpected end of JSON input'); },
        })));

        const result = await bffGet(URL);

        expect(result.ok).toBe(true);
        expect(result.data).toBeNull();
    });

    describe('BffResult monad', () => {
        it('map transforms ok data and passes err through', () => {
            const error = new BffApiError(500, 'server_error', 'boom');
            const errResult = BffResult.err(error);

            expect(BffResult.ok(1).map((n) => n + 1).data).toBe(2);
            expect(errResult.map((n) => n + 1)).toBe(errResult);
        });

        it('mapErr transforms err and passes ok through', () => {
            const okResult = BffResult.ok(1);
            const mapped = BffResult.err(new BffApiError(500, 'server_error', 'boom'))
                .mapErr((e) => new BffApiError(e.status, 'mapped', e.message));

            expect(okResult.mapErr(() => null)).toBe(okResult);
            expect(mapped.error.code).toBe('mapped');
        });

        it('match reduces both sides to a plain value', () => {
            expect(BffResult.ok(1).match({ ok: (d) => 'ok:' + d, err: () => 'err' })).toBe('ok:1');
            expect(
                BffResult.err(new BffApiError(404, 'route_not_found', 'nope'))
                    .match({ ok: () => 'ok', err: (e) => 'err:' + e.code })
            ).toBe('err:route_not_found');
        });
    });

    it('maps validation errors with per-field details', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => jsonResponse(422, {
            status: 422,
            success: false,
            error: {
                code: 'unprocessable_entity',
                message: 'The given data failed to be processed.',
                details: [{ param: 'title', errors: ['The title field is required.'] }],
            },
        })));

        const result = await bffPut(URL, {});

        expect(result.ok).toBe(false);
        expect(result.error).toBeInstanceOf(BffApiError);
        expect(result.error.isValidation()).toBe(true);
        expect(result.error.validationErrors).toEqual([
            { param: 'title', errors: ['The title field is required.'] },
        ]);
    });

    it('redirects to the login page on 401 and still returns an err result', async () => {
        const assign = stubLocationAssign();
        vi.stubGlobal('fetch', vi.fn(async () => jsonResponse(401, {
            status: 401,
            success: false,
            error: { code: 'unauthenticated', message: 'You are not authenticated for this request.' },
        })));

        const result = await bffGet(URL);

        expect(assign).toHaveBeenCalledWith('/auth/login');
        expect(result.ok).toBe(false);
        expect(result.error.isUnauthenticated()).toBe(true);
        expect(result.error.code).toBe('unauthenticated');
    });

    it('skips the 401 redirect when opted out', async () => {
        const assign = stubLocationAssign();
        vi.stubGlobal('fetch', vi.fn(async () => jsonResponse(401, {
            status: 401,
            success: false,
            error: { code: 'unauthenticated', message: 'You are not authenticated for this request.' },
        })));

        const result = await request(URL, { redirectOnUnauthenticated: false });

        expect(assign).not.toHaveBeenCalled();
        expect(result.ok).toBe(false);
    });

    it.each([
        [403, 'isUnauthorized'],
        [429, 'isTooManyRequests'],
    ])('exposes predicate %s via %s()', async (status, predicate) => {
        vi.stubGlobal('fetch', vi.fn(async () => jsonResponse(status, {
            status: status,
            success: false,
            error: { code: 'some_error', message: 'msg' },
        })));

        const result = await bffGet(URL);

        expect(result.error[predicate]()).toBe(true);
    });

    it('normalizes network failures into a BffApiError without throwing', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => { throw new TypeError('Failed to fetch'); }));

        const result = await bffGet(URL);

        expect(result.ok).toBe(false);
        expect(result.error).toBeInstanceOf(BffApiError);
        expect(result.error.status).toBe(0);
        expect(result.error.code).toBe('network_error');
        expect(result.response).toBeNull();
    });
});
