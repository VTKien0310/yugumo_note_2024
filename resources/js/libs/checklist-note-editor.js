import { bffDelete, bffPost, bffPut } from '../services/bff-api';

const SYNC_DEBOUNCE_MS = 500;

/**
 * Alpine component backing the checklist note editor.
 *
 * Every item row gets its own x-data scope built by the exported `item`
 * factory, so per-item sync state (saving/saved/failed) is tracked
 * independently. Syncs debounce per target and a per-target sequence number
 * makes the latest write win - a stale in-flight response can never
 * overwrite state that a newer request has already moved past.
 */
export default function checklistNoteEditor({ titleSyncUrl, storeUrl, initialTitle }) {
    let lastSyncedTitle = initialTitle;
    let titleTimer = null;
    let titleSyncSeq = 0;
    let itemSeq = 0;

    return {
        // Reactive state - everything Alpine owns carries the `alp` prefix.
        alpTitle: initialTitle,
        alpTitleSaveState: 'saved', // 'saved' | 'saving' | 'failed'
        alpTitleUnsaved: false,
        alpAdding: false,
        alpAddFailed: false,
        // Items the server confirmed during this session. Prepended so a new
        // item lands at the top of the incomplete section, matching the
        // server-side display ordering (is_completed ASC, id DESC).
        alpAddedItems: [],

        destroy() {
            clearTimeout(titleTimer);
        },

        alpHandleTitleInput() {
            this.alpTitleUnsaved = this.alpTitle !== lastSyncedTitle;
            this.alpScheduleTitleSync();
        },

        alpScheduleTitleSync() {
            clearTimeout(titleTimer);
            titleTimer = setTimeout(() => this.alpSyncTitle(), SYNC_DEBOUNCE_MS);
        },

        async alpSyncTitle() {
            if (! this.alpTitleUnsaved) return;

            const title = this.alpTitle;
            const seq = ++titleSyncSeq;

            this.alpTitleSaveState = 'saving';

            const result = await bffPut(titleSyncUrl, { title: title });

            // A newer sync was scheduled while this one was in flight - its
            // response owns the state now.
            if (seq !== titleSyncSeq) return;

            result.match({
                ok: () => {
                    lastSyncedTitle = title;
                    this.alpTitleUnsaved = this.alpTitle !== lastSyncedTitle;
                    this.alpTitleSaveState = 'saved';
                },
                err: () => {
                    this.alpTitleUnsaved = true;
                    this.alpTitleSaveState = 'failed';
                },
            });
        },

        async alpAddItem() {
            if (this.alpAdding) return;

            this.alpAdding = true;
            this.alpAddFailed = false;

            const result = await bffPost(storeUrl);

            result.match({
                ok: (data) => {
                    // New items are incomplete and therefore sort to the top of
                    // the incomplete section (is_completed ASC, id DESC).
                    this.alpAddedItems.unshift({
                        id: data.id,
                        content: data.content,
                        isCompleted: Boolean(data.is_completed),
                        updateUrl: `${storeUrl}/${data.id}`,
                        deleteUrl: `${storeUrl}/${data.id}`,
                        position: Number.MAX_SAFE_INTEGER - itemSeq++,
                    });
                    this.$nextTick(() => {
                        const input = document.getElementById(`checklist-item-${data.id}-content`);
                        input?.focus();
                        input?.select();
                    });
                },
                err: () => {
                    this.alpAddFailed = true;
                },
            });

            this.alpAdding = false;
        },
    };
}

/**
 * Per-item Alpine state. Bound via x-data on each row; `position` is the
 * row's index in the parent list at mount time, used to roll a failed delete
 * back to its original spot.
 *
 * Registered under its own provider name (checklistNoteEditorItem): Alpine
 * data providers are injected as getters that invoke the factory, so the
 * per-row factory must be its own registration rather than a static property
 * on the parent factory.
 */
export function checklistNoteEditorItem({ id, content, isCompleted, updateUrl, deleteUrl, position = 0 }) {
    let syncTimer = null;
    let syncSeq = 0;

    return {
        alpId: id,
        alpContent: content,
        alpIsCompleted: Boolean(isCompleted),
        alpDeleted: false,
        alpSaveState: 'saved', // 'saved' | 'saving' | 'failed'

        destroy() {
            clearTimeout(syncTimer);
        },

        alpHandleContentInput() {
            clearTimeout(syncTimer);
            syncTimer = setTimeout(() => this.alpSyncItem(), SYNC_DEBOUNCE_MS);
        },

        alpHandleCompletionToggle() {
            this.alpSyncItem();
        },

        async alpSyncItem() {
            clearTimeout(syncTimer);

            const content = this.alpContent;
            const isCompleted = this.alpIsCompleted;
            const seq = ++syncSeq;

            this.alpSaveState = 'saving';

            const result = await bffPut(updateUrl, {
                content: content,
                is_completed: isCompleted,
            });

            if (seq !== syncSeq) return;

            result.match({
                ok: () => {
                    this.alpSaveState = 'saved';
                },
                err: () => {
                    this.alpSaveState = 'failed';
                },
            });
        },

        async alpDeleteItem() {
            if (this.alpDeleted) return;

            // Optimistic removal - the row hides immediately, the request
            // confirms it.
            this.alpDeleted = true;

            const result = await bffDelete(deleteUrl);

            result.match({
                ok: () => {},
                err: () => {
                    // Roll back: restore the row at its original position.
                    this.alpDeleted = false;
                    this.alpSaveState = 'failed';
                },
            });
        },
    };
};
