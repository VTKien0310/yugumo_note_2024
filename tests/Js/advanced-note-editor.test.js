import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import Alpine from 'alpinejs';
import advancedNoteEditor from '../../resources/js/advanced-note-editor.js';

const SYNC_URL = 'http://localhost/bff/notes/01TEST';
const INITIAL_TITLE = 'Initial title';
const INITIAL_CONTENT = { ops: [{ insert: 'initial\n' }] };

let alpineStarted = false;

/**
 * Renders the markup of the advanced note Blade component and boots Alpine on
 * it, mirroring `edit-advanced-note-form-livewire.blade.php`.
 */
function mountEditor() {
    document.head.innerHTML = '<meta name="csrf-token" content="test-token">';
    document.body.innerHTML = `
        <div id="root">
            <input x-ref="alpTitleInput" value="${INITIAL_TITLE}">
            <div wire:ignore>
                <div x-ref="alpEditor" class="ql-editor-host"></div>
            </div>
        </div>
    `;

    // Set via setAttribute: the serialised content contains double quotes,
    // which would terminate the attribute if inlined into the markup above.
    document.getElementById('root').setAttribute('x-data', `advancedNoteEditor(${JSON.stringify({
        syncUrl: SYNC_URL,
        initialTitle: INITIAL_TITLE,
        initialContent: INITIAL_CONTENT,
    })})`);

    if (! alpineStarted) {
        Alpine.data('advancedNoteEditor', advancedNoteEditor);
        Alpine.start();
        alpineStarted = true;
    } else {
        Alpine.initTree(document.body);
    }

    const root = document.getElementById('root');

    return {
        root,
        quill: root._alpQuill,
        // The reactive x-data object, as Alpine exposes it to the markup.
        state: Alpine.$data(root),
    };
}

/**
 * Quill reacts to DOM edits through a MutationObserver, which is async.
 * `quill.update()` drains pending records synchronously instead.
 */
function flushQuill(quill) {
    quill.update();
}

describe('advancedNoteEditor', () => {
    beforeEach(() => {
        vi.stubGlobal('fetch', vi.fn(async () => ({ ok: true, json: async () => ({}) })));
    });

    afterEach(() => {
        vi.unstubAllGlobals();
        document.body.innerHTML = '';
    });

    it('keeps the Quill instance outside of Alpine reactive proxy', () => {
        const { quill } = mountEditor();

        // Regression guard. When Quill lived on `x-data`, Alpine's deep
        // reactive Proxy re-wrapped `quill.scroll` on every read, so the raw
        // identity below no longer held.
        expect(quill.scroll).toBe(Alpine.raw(quill.scroll));
        expect(quill.root).toBe(Alpine.raw(quill.root));
    });

    it('resolves blots from DOM nodes', () => {
        const { quill } = mountEditor();

        // `ScrollBlot.find()` checks `blot.scroll === this`. Through a Proxy
        // that identity check fails and every lookup returns null, which is
        // what crashed `Selection.normalizedToRange()` with
        // "Cannot read properties of null (reading 'offset')".
        const blot = quill.scroll.find(quill.root.firstChild, true);

        expect(blot).not.toBeNull();
        expect(() => blot.offset(quill.scroll)).not.toThrow();
    });

    it('renders the initial content', () => {
        const { quill } = mountEditor();

        expect(quill.getText()).toBe('initial\n');
    });

    it('reflects edits in getContents and flags unsaved changes', () => {
        const { quill, state } = mountEditor();

        quill.setText('updated text\n');
        flushQuill(quill);

        // With the editor proxied, the SCROLL_UPDATE handler threw before it
        // could refresh `editor.delta`, so getContents() kept returning the
        // initial document and nothing was ever reported as unsaved.
        expect(quill.getText()).toBe('updated text\n');
        expect(state.alpHasUnsavedChanges()).toBe(true);
    });

    it('syncs the updated content to the server', async () => {
        const { quill, state } = mountEditor();

        quill.setText('updated text\n');
        flushQuill(quill);

        await state.alpSync();

        expect(fetch).toHaveBeenCalledOnce();

        const [url, options] = fetch.mock.calls[0];
        const body = JSON.parse(options.body);

        expect(url).toBe(SYNC_URL);
        expect(options.method).toBe('PUT');
        expect(options.headers['X-CSRF-TOKEN']).toBe('test-token');
        expect(body.title).toBe(INITIAL_TITLE);
        expect(body.content.ops[0].insert).toBe('updated text\n');

        expect(state.alpSaveState).toBe('saved');
        expect(state.alpUnsaved).toBe(false);
    });

    it('syncs an updated title', async () => {
        const { root, state } = mountEditor();

        root.querySelector('input').value = 'Renamed';

        await state.alpSync();

        expect(JSON.parse(fetch.mock.calls[0][1].body).title).toBe('Renamed');
    });

    it('does not sync when nothing changed', () => {
        const { state } = mountEditor();

        state.alpTick();

        expect(fetch).not.toHaveBeenCalled();
        expect(state.alpUnsaved).toBe(false);
    });

    it('marks the save state as failed when the request fails', async () => {
        const { quill, state } = mountEditor();

        vi.stubGlobal('fetch', vi.fn(async () => ({
            ok: false,
            status: 500,
            json: async () => ({ status: 500, success: false, error: { code: 'server_error', message: 'Internal server error.' } }),
        })));

        quill.setText('updated text\n');
        flushQuill(quill);

        await state.alpSync();

        expect(state.alpSaveState).toBe('failed');
        expect(state.alpUnsaved).toBe(true);
    });

    it('redirects to the login page on 401 and marks the save as failed', async () => {
        const { quill, state } = mountEditor();

        const assign = vi.fn();
        Object.defineProperty(window, 'location', {
            configurable: true,
            value: { ...window.location, assign: assign },
        });

        vi.stubGlobal('fetch', vi.fn(async () => ({
            ok: false,
            status: 401,
            json: async () => ({
                status: 401,
                success: false,
                error: { code: 'unauthenticated', message: 'You are not authenticated for this request.' },
            }),
        })));

        quill.setText('updated text\n');
        flushQuill(quill);

        await state.alpSync();

        expect(assign).toHaveBeenCalledWith('/auth/login');
        expect(state.alpSaveState).toBe('failed');
        expect(state.alpUnsaved).toBe(true);
    });
});
