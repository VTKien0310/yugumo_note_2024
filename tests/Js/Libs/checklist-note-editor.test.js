import { beforeEach, describe, expect, it, vi } from 'vitest';
import Alpine from 'alpinejs';
import checklistNoteEditor, { checklistNoteEditorItem } from '../../../resources/js/libs/checklist-note-editor.js';
import { makeChecklistItem } from '../Factories/NoteFixture.js';

const TITLE_SYNC_URL = 'http://localhost/bff/notes/01TESTNOTE';
const STORE_URL = 'http://localhost/bff/notes/01TESTNOTE/checklist-items';
const ITEM_URL = `${STORE_URL}/01TESTITEM`;
const INITIAL_TITLE = 'My checklist';
const INITIAL_ITEM = makeChecklistItem({
    id: '01TESTITEM',
    storeUrl: STORE_URL,
});

let alpineStarted = false;

/**
 * Renders the markup of the checklist note Blade component and boots Alpine
 * on it, mirroring edit-checklist-note-form-livewire.blade.php.
 */
function mountEditor({ withItem = true } = {}) {
    document.body.innerHTML = `
        <div id="root">
            <input id="title" x-ref="alpTitleInput" x-model="alpTitle" x-on:input="alpHandleTitleInput()" value="${INITIAL_TITLE}">
            <button id="add" x-on:click="alpAddItem()" x-bind:disabled="alpAdding"></button>
            <template x-for="alpItem in alpAddedItems" :key="alpItem.id">
                <div x-data="checklistNoteEditorItem(alpItem)" x-show="! alpDeleted" class="added-row">
                    <input type="text" x-bind:id="\`checklist-item-\${alpId}-content\`" x-model="alpContent" x-on:input="alpHandleContentInput()">
                    <input type="checkbox" x-model="alpIsCompleted" x-on:change="alpHandleCompletionToggle()">
                    <button class="delete" x-on:click="alpDeleteItem()"></button>
                </div>
            </template>
            ${
                withItem
                    ? `
                <div id="server-row" x-data='checklistNoteEditorItem(${JSON.stringify(INITIAL_ITEM)})' x-show="! alpDeleted">
                    <input type="text" id="item-content" x-model="alpContent" x-on:input="alpHandleContentInput()" value="${INITIAL_ITEM.content}">
                    <input type="checkbox" id="item-completed" x-model="alpIsCompleted" x-on:change="alpHandleCompletionToggle()">
                    <button id="delete" x-on:click="alpDeleteItem()"></button>
                </div>
            `
                    : ''
            }
        </div>
    `;

    document.getElementById('root').setAttribute('x-data', `checklistNoteEditor(${JSON.stringify({
        titleSyncUrl: TITLE_SYNC_URL,
        storeUrl: STORE_URL,
        initialTitle: INITIAL_TITLE,
    })})`);

    if (! alpineStarted) {
        Alpine.data('checklistNoteEditor', checklistNoteEditor);
        Alpine.data('checklistNoteEditorItem', checklistNoteEditorItem);
        Alpine.start();
        alpineStarted = true;
    } else {
        Alpine.initTree(document.body);
    }

    const root = document.getElementById('root');

    return {
        root,
        state: Alpine.$data(root),
        itemState: withItem ? Alpine.$data(document.getElementById('server-row')) : null,
    };
}

function okResponse(data = {}) {
    return { ok: true, status: 200, json: async () => ({ status: 200, success: true, data }) };
}

function errorResponse(status = 500) {
    return {
        ok: false,
        status,
        json: async () => ({ status, success: false, error: { code: 'server_error', message: 'Internal server error.' } }),
    };
}

describe('checklistNoteEditor', () => {
    beforeEach(() => {
        vi.useFakeTimers();
        vi.stubGlobal('fetch', vi.fn(async () => okResponse()));

        return () => {
            vi.useRealTimers();
        };
    });

    it('debounces title edits and syncs them to the note endpoint', async () => {
        const { root, state } = mountEditor();

        const titleInput = root.querySelector('#title');
        titleInput.value = 'Renamed checklist';
        titleInput.dispatchEvent(new Event('input'));

        expect(fetch).not.toHaveBeenCalled();

        await vi.advanceTimersByTimeAsync(600);

        expect(fetch).toHaveBeenCalledOnce();

        const [url, options] = fetch.mock.calls[0];
        expect(url).toBe(TITLE_SYNC_URL);
        expect(options.method).toBe('PUT');
        expect(options.headers['X-CSRF-TOKEN']).toBe('test-token');
        expect(JSON.parse(options.body)).toEqual({ title: 'Renamed checklist' });

        expect(state.alpTitleSaveState).toBe('saved');
        expect(state.alpTitleUnsaved).toBe(false);
    });

    it('collapses rapid title edits into a single sync', async () => {
        const { root } = mountEditor();

        const titleInput = root.querySelector('#title');
        for (const value of ['R', 'Re', 'Renamed']) {
            titleInput.value = value;
            titleInput.dispatchEvent(new Event('input'));
            await vi.advanceTimersByTimeAsync(100);
        }

        await vi.advanceTimersByTimeAsync(600);

        expect(fetch).toHaveBeenCalledOnce();
        expect(JSON.parse(fetch.mock.calls[0][1].body)).toEqual({ title: 'Renamed' });
    });

    it('marks the title sync as failed when the request fails', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => errorResponse()));

        const { root, state } = mountEditor();

        const titleInput = root.querySelector('#title');
        titleInput.value = 'Renamed checklist';
        titleInput.dispatchEvent(new Event('input'));

        await vi.advanceTimersByTimeAsync(600);

        expect(state.alpTitleSaveState).toBe('failed');
        expect(state.alpTitleUnsaved).toBe(true);
    });

    it('syncs item content edits after the debounce window', async () => {
        const { root, itemState } = mountEditor();

        const contentInput = root.querySelector('#item-content');
        contentInput.value = 'Buy oat milk';
        contentInput.dispatchEvent(new Event('input'));

        await vi.advanceTimersByTimeAsync(600);

        expect(fetch).toHaveBeenCalledOnce();

        const [url, options] = fetch.mock.calls[0];
        expect(url).toBe(ITEM_URL);
        expect(options.method).toBe('PUT');
        expect(JSON.parse(options.body)).toEqual({ content: 'Buy oat milk', is_completed: false });

        expect(itemState.alpSaveState).toBe('saved');
    });

    it('syncs completion toggles immediately, without debounce', async () => {
        const { root, itemState } = mountEditor();

        const checkbox = root.querySelector('#item-completed');
        checkbox.checked = true;
        checkbox.dispatchEvent(new Event('change'));

        await vi.advanceTimersByTimeAsync(0);

        expect(fetch).toHaveBeenCalledOnce();
        expect(JSON.parse(fetch.mock.calls[0][1].body)).toEqual({ content: 'Buy milk', is_completed: true });
        expect(itemState.alpIsCompleted).toBe(true);
    });

    it('lets the latest item write win when edits happen mid-flight', async () => {
        let releaseFirst;
        const firstRequest = new Promise((resolve) => {
            releaseFirst = () => resolve(okResponse());
        });

        vi.stubGlobal('fetch', vi.fn()
            .mockImplementationOnce(() => firstRequest)
            .mockImplementationOnce(async () => okResponse()));

        const { root, itemState } = mountEditor();

        const contentInput = root.querySelector('#item-content');
        contentInput.value = 'First edit';
        contentInput.dispatchEvent(new Event('input'));
        await vi.advanceTimersByTimeAsync(600);

        contentInput.value = 'Second edit';
        contentInput.dispatchEvent(new Event('input'));
        await vi.advanceTimersByTimeAsync(600);

        expect(fetch).toHaveBeenCalledTimes(2);

        const secondSettled = vi.waitFor(() => {
            if (fetch.mock.calls.length < 2) throw new Error('waiting');
        });
        await secondSettled;
        releaseFirst();
        await vi.advanceTimersByTimeAsync(0);

        expect(JSON.parse(fetch.mock.calls[1][1].body)).toEqual({ content: 'Second edit', is_completed: false });
        expect(itemState.alpSaveState).toBe('saved');
    });

    it('deletes an item optimistically and keeps it hidden on success', async () => {
        const { root, itemState } = mountEditor();

        root.querySelector('#delete').dispatchEvent(new Event('click'));

        expect(itemState.alpDeleted).toBe(true);

        await vi.advanceTimersByTimeAsync(0);

        expect(fetch).toHaveBeenCalledOnce();
        expect(fetch.mock.calls[0][0]).toBe(ITEM_URL);
        expect(fetch.mock.calls[0][1].method).toBe('DELETE');
        expect(itemState.alpDeleted).toBe(true);
    });

    it('restores the item when deletion fails', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => errorResponse()));

        const { root, itemState } = mountEditor();

        root.querySelector('#delete').dispatchEvent(new Event('click'));
        await vi.advanceTimersByTimeAsync(0);

        expect(itemState.alpDeleted).toBe(false);
        expect(itemState.alpSaveState).toBe('failed');
    });

    it('creates a new item via the store endpoint and renders it on top', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => okResponse({
            id: '01NEWITEM',
            content: 'Untitled',
            is_completed: false,
        })));

        const { root, state } = mountEditor({ withItem: false });

        root.querySelector('#add').dispatchEvent(new Event('click'));
        await vi.advanceTimersByTimeAsync(0);

        expect(fetch).toHaveBeenCalledOnce();
        expect(fetch.mock.calls[0][0]).toBe(STORE_URL);
        expect(fetch.mock.calls[0][1].method).toBe('POST');

        expect(state.alpAddedItems).toHaveLength(1);
        expect(state.alpAddedItems[0]).toMatchObject({
            id: '01NEWITEM',
            content: 'Untitled',
            isCompleted: false,
            updateUrl: `${STORE_URL}/01NEWITEM`,
            deleteUrl: `${STORE_URL}/01NEWITEM`,
        });
        expect(state.alpAdding).toBe(false);
        expect(state.alpAddFailed).toBe(false);

        await vi.advanceTimersByTimeAsync(0);
        expect(root.querySelector('.added-row')).not.toBeNull();
        expect(document.getElementById('checklist-item-01NEWITEM-content')).not.toBeNull();
    });

    it('shows a failed state and re-enables the button when creation fails', async () => {
        vi.stubGlobal('fetch', vi.fn(async () => errorResponse()));

        const { root, state } = mountEditor({ withItem: false });

        root.querySelector('#add').dispatchEvent(new Event('click'));
        await vi.advanceTimersByTimeAsync(0);

        expect(state.alpAddFailed).toBe(true);
        expect(state.alpAdding).toBe(false);
        expect(state.alpAddedItems).toHaveLength(0);
    });

    it('redirects to the login page on 401', async () => {
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

        const { root } = mountEditor();

        const titleInput = root.querySelector('#title');
        titleInput.value = 'Renamed checklist';
        titleInput.dispatchEvent(new Event('input'));

        await vi.advanceTimersByTimeAsync(600);

        expect(assign).toHaveBeenCalledWith('/auth/login');
    });
});
