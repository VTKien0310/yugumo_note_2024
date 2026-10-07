import EasyMDE from 'easymde';
import { bffPut } from '../services/bff-api';

const SYNC_INTERVAL_MS = 3000;

/**
 * Alpine component backing the markdown note editor.
 *
 * The EasyMDE instance and the sync snapshots are held in closure variables on
 * purpose - they are NOT properties of the returned `x-data` object. Alpine
 * wraps that object in a deep reactive Proxy, and CodeMirror (which EasyMDE
 * wraps) relies on raw object identity internally; reading the instance back
 * through the Proxy breaks those identity checks. This is the same pitfall
 * documented for the Quill-backed advanced note editor.
 */
export default function markdownNoteEditor({ syncUrl, initialTitle, initialContent }) {
    let easyMde = null;
    let lastSyncedTitle = initialTitle;
    let lastSyncedContent = initialContent;
    let syncTimer = null;
    let beforeUnloadHandler = null;

    return {
        alpSyncing: false,
        alpUnsaved: false,
        alpSaveState: 'saved',

        init() {
            easyMde = new EasyMDE({
                element: this.$refs.alpEditor,
                initialValue: initialContent,
                spellChecker: false,
            });

            easyMde.codemirror.on('change', () => {
                this.alpUnsaved = this.alpHasUnsavedChanges();
            });

            // Expose the raw instance for tests. DOM elements are never wrapped
            // by Alpine's reactivity, so this handle stays proxy-free.
            this.$el._alpEasyMde = easyMde;

            beforeUnloadHandler = (event) => {
                if (! this.alpHasUnsavedChanges()) return;

                this.alpSync({ keepalive: true });

                event.preventDefault();
                event.returnValue = '';
            };
            window.addEventListener('beforeunload', beforeUnloadHandler);

            syncTimer = setInterval(() => this.alpTick(), SYNC_INTERVAL_MS);
        },

        destroy() {
            clearInterval(syncTimer);
            window.removeEventListener('beforeunload', beforeUnloadHandler);
        },

        alpHasUnsavedChanges() {
            if (! easyMde) return false;

            const title = this.$refs.alpTitleInput.value;
            const content = easyMde.value();

            return title !== lastSyncedTitle || content !== lastSyncedContent;
        },

        alpTick() {
            this.alpUnsaved = this.alpHasUnsavedChanges();

            if (! this.alpUnsaved || this.alpSyncing) return;

            this.alpSync();
        },

        async alpSync({ keepalive = false } = {}) {
            if (! easyMde || this.alpSyncing) return;

            // Snapshot before fetch so edits made during flight stay marked unsaved.
            const title = this.$refs.alpTitleInput.value;
            const content = easyMde.value();

            this.alpSyncing = true;
            this.alpSaveState = 'saving';

            const result = await bffPut(syncUrl, {
                title: title,
                content: content,
            }, { keepalive: keepalive });

            result.match({
                ok: () => {
                    lastSyncedTitle = title;
                    lastSyncedContent = content;
                    this.alpUnsaved = this.alpHasUnsavedChanges();
                    this.alpSaveState = 'saved';
                },
                err: () => {
                    this.alpUnsaved = this.alpHasUnsavedChanges();
                    this.alpSaveState = 'failed';
                },
            });

            this.alpSyncing = false;
        },
    };
}
