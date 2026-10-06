const LOGIN_URL = '/auth/login';
const NETWORK_ERROR_CODE = 'network_error';

/**
 * Typed error returned by every failed BFF call.
 *
 * Covers both HTTP-level failures (the server answered with a non-2xx JSON
 * error payload shaped by JsonApiExceptionHandler) and network-level failures
 * (offline, DNS, CORS - fetch rejected before any response arrived), so
 * callers only ever have one error type to handle.
 */
export class BffApiError extends Error {
    /**
     * @param {number} status HTTP status code, or 0 for network-level failure.
     * @param {string} code Server error code (see CommonHttpErrorCodeEnum) or 'network_error'.
     * @param {string} message Human-readable error message.
     * @param {Array|null} validationErrors Per-field `error.details` payload of a 422 response.
     */
    constructor(status, code, message, validationErrors = null) {
        super(message);
        this.name = 'BffApiError';
        this.status = status;
        this.code = code;
        this.validationErrors = validationErrors;
    }

    static fromResponse(status, payload) {
        const error = payload?.error ?? {};

        return new BffApiError(
            status,
            error.code ?? 'unknown_error',
            error.message ?? 'Request failed with status ' + status,
            error.details ?? null
        );
    }

    static networkError(cause) {
        return new BffApiError(0, NETWORK_ERROR_CODE, 'Network request failed: ' + (cause?.message ?? cause));
    }

    isValidation() {
        return this.status === 422;
    }

    isUnauthenticated() {
        return this.status === 401;
    }

    isUnauthorized() {
        return this.status === 403;
    }

    isTooManyRequests() {
        return this.status === 429;
    }
}

/**
 * Minimal Result monad wrapping a BFF response.
 *
 * The client never throws: every call resolves to either BffResult.ok(data)
 * or BffResult.err(BffApiError), and callers reduce it to UI state through
 * `match()` or transform it through `map()` / `mapErr()`.
 *
 * Deliberately minimal. Planned extensions - add one only when a second real
 * caller needs it, do not unwrap the type ad hoc at call sites instead:
 *
 *   - chain(fn) / flatMap: sequential dependent BFF calls, where fn returns
 *     another BffResult.
 *   - mapAsync(fn) / matchAsync({...}): async transforms and reductions
 *     without leaving the monad.
 *   - BffResult.fromPromise(promise): adopt non-BFF async work (e.g. another
 *     fetch-based service) into this same type.
 *   - tap(fn) / tapErr(fn): side effects such as logging or telemetry in the
 *     middle of a pipeline.
 *   - getOrElse(fallback) / getOrThrow(): escape hatches at the call site.
 *   - BffResult.all([...]): applicative fan-out for parallel calls,
 *     accumulating errors.
 */
export class BffResult {
    /**
     * @param {boolean} ok
     * @param {*} data Unwrapped `data` payload on success, null on failure.
     * @param {BffApiError|null} error
     * @param {Response|null} response Raw fetch Response, null on network failure.
     */
    constructor(ok, data, error, response) {
        this.ok = ok;
        this.data = data;
        this.error = error;
        this.response = response;
    }

    static ok(data, response = null) {
        return new BffResult(true, data, null, response);
    }

    static err(error, response = null) {
        return new BffResult(false, null, error, response);
    }

    map(fn) {
        return this.ok ? BffResult.ok(fn(this.data), this.response) : this;
    }

    mapErr(fn) {
        return this.ok ? this : BffResult.err(fn(this.error), this.response);
    }

    match({ ok, err }) {
        return this.ok ? ok(this.data, this.response) : err(this.error, this.response);
    }
}

function csrfToken() {
    return document.querySelector('meta[name=csrf-token]')?.content ?? '';
}

/**
 * Performs a request against the session-authenticated BFF JSON backend.
 *
 * Never throws: any failure - HTTP error or network error - is returned as a
 * BffResult.err. On 401 the browser is redirected to the login page (the
 * session has expired), unless opted out via `redirectOnUnauthenticated`.
 *
 * @param {string} url
 * @param {object} options
 * @param {string} [options.method='GET']
 * @param {*} [options.body] JSON-serializable request body.
 * @param {boolean} [options.keepalive=false] Forwarded to fetch, for beforeunload syncs.
 * @param {boolean} [options.redirectOnUnauthenticated=true]
 * @returns {Promise<BffResult>}
 */
export async function request(url, { method = 'GET', body, keepalive = false, redirectOnUnauthenticated = true } = {}) {
    let response;

    try {
        response = await fetch(url, {
            method: method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: body === undefined ? undefined : JSON.stringify(body),
            keepalive: keepalive,
        });
    } catch (cause) {
        return BffResult.err(BffApiError.networkError(cause));
    }

    const payload = await response.json?.().catch(() => null) ?? null;

    if (response.ok) {
        return BffResult.ok(payload?.data ?? null, response);
    }

    if (response.status === 401 && redirectOnUnauthenticated) {
        window.location.assign(LOGIN_URL);
    }

    return BffResult.err(BffApiError.fromResponse(response.status, payload), response);
}

export function bffGet(url, options = {}) {
    return request(url, { ...options, method: 'GET' });
}

export function bffPost(url, body, options = {}) {
    return request(url, { ...options, method: 'POST', body });
}

export function bffPut(url, body, options = {}) {
    return request(url, { ...options, method: 'PUT', body });
}

export function bffPatch(url, body, options = {}) {
    return request(url, { ...options, method: 'PATCH', body });
}

export function bffDelete(url, options = {}) {
    return request(url, { ...options, method: 'DELETE' });
}
