import { beforeEach, describe, expect, it, vi } from 'vitest';
import Alpine from 'alpinejs';
import markdownNoteEditor from '../../../resources/js/libs/markdown-note-editor.js';

const SYNC_URL = 'http://localhost/bff/notes/01TEST';
const INITIAL_TITLE = 'Initial title';
const INITIAL_CONTENT = '# Heading\n\nSome **markdown** text\n';

let alpineStarted = false;

/**
 * Renders the markup of the markdown note Blade component and boots Alpine on
 * it, mirroring `edit-markdown-note-form-livewire.blade.php`.
 */
function mountEditor() {
    document.body.innerHTML = `
        <div id="root">
            <input x-ref="alpTitleInput" value="${INITIAL_TITLE}">
            <div wire:ignore>
                <textarea x-ref="alpEditor" class="easymde-editor-host"></textarea>
            </div>
        </div>
    `;

    document.getElementById('root').setAttribute('x-data', `markdownNoteEditor(${JSON.stringify({
        syncUrl: SYNC_URL,
        initialTitle: INITIAL_TITLE,
        initialContent: INITIAL_CONTENT,
    })})`);

    if (! alpineStarted) {
        Alpine.data('markdownNoteEditor', markdownNoteEditor);
        Alpine.start();
        alpineStarted = true;
    } else {
        Alpine.initTree(document.body);
    }

    const root = document.getElementById('root');

    return {
        root,
        easyMde: root._alpEasyMde,
        state: Alpine.$data(root),
    };
}

describe('markdownNoteEditor', () => {
    beforeEach(() => {
        vi.stubGlobal('fetch', vi.fn(async () => ({ ok: true, json: async () => ({}) })));
    });

    it('keeps the EasyMDE instance outside of Alpine reactive proxy', () => {
        const { easyMde } = mountEditor();

        expect(easyMde.codemirror).toBe(Alpine.raw(easyMde.codemirror));
    });

    it('renders the initial content', () => {
        const { easyMde } = mountEditor();

        expect(easyMde.value()).toBe(INITIAL_CONTENT);
    });

    it('flags unsaved changes when the content is edited', () => {
        const { easyMde, state } = mountEditor();

        easyMde.value('updated markdown\n');

        expect(easyMde.value()).toBe('updated markdown\n');
        expect(state.alpHasUnsavedChanges()).toBe(true);
    });

    it('syncs the updated content to the server', async () => {
        const { easyMde, state } = mountEditor();

        easyMde.value('updated markdown\n');

        await state.alpSync();

        expect(fetch).toHaveBeenCalledOnce();

        const [url, options] = fetch.mock.calls[0];
        const body = JSON.parse(options.body);

        expect(url).toBe(SYNC_URL);
        expect(options.method).toBe('PUT');
        expect(options.headers['X-CSRF-TOKEN']).toBe('test-token');
        expect(body.title).toBe(INITIAL_TITLE);
        expect(body.content).toBe('updated markdown\n');

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
        const { easyMde, state } = mountEditor();

        vi.stubGlobal('fetch', vi.fn(async () => ({
            ok: false,
            status: 500,
            json: async () => ({ status: 500, success: false, error: { code: 'server_error', message: 'Internal server error.' } }),
        })));

        easyMde.value('updated markdown\n');

        await state.alpSync();

        expect(state.alpSaveState).toBe('failed');
        expect(state.alpUnsaved).toBe(true);
    });
});
