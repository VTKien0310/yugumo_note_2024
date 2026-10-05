import Quill from 'quill';
import { bffPut } from './services/bff-api';

const SYNC_INTERVAL_MS = 3000;

const QUILL_TOOLBAR = [
    [{ header: [1, 2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ color: [] }, { background: [] }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'code-block'],
    ['link'],
    ['clean'],
];

/**
 * Alpine component backing the advanced (rich text) note editor.
 *
 * The Quill instance and the sync snapshots are held in closure variables on
 * purpose - they are NOT properties of the returned `x-data` object.
 *
 * Alpine wraps the object returned to `x-data` in a deep reactive Proxy, and
 * that Proxy re-wraps every object-valued property on read. Parchment (Quill's
 * document model) keys blots by raw object identity: `Registry.blots` is a
 * WeakMap keyed on the raw DOM node holding raw blots, and `ScrollBlot.find()`
 * verifies ownership with `blot.scroll === this`. Reading `quill.scroll`
 * through the Proxy yields a wrapped scroll, that identity check fails, and
 * `find()` returns null for every node.
 *
 * The visible symptom is `Selection.normalizedToRange()` throwing
 * "Cannot read properties of null (reading 'offset')" on every DOM mutation.
 * The damaging symptom is silent: the throw aborts Quill's SCROLL_UPDATE
 * handler before it can refresh `editor.delta`, so `getContents()` keeps
 * returning the document as it looked at init and the note content is never
 * synced - while the title, read straight off the DOM input, saves fine.
 *
 * Keeping Quill out of the reactive graph entirely is what prevents this.
 */
export default function advancedNoteEditor({ syncUrl, initialTitle, initialContent }) {
    let quill = null;
    let lastSyncedTitle = initialTitle;
    let lastSyncedContent = JSON.stringify(initialContent);
    let syncTimer = null;
    let beforeUnloadHandler = null;

    return {
        alpSyncing: false,
        alpUnsaved: false,
        alpSaveState: 'saved',

        init() {
            quill = new Quill(this.$refs.alpEditor, {
                theme: 'snow',
                modules: {
                    toolbar: QUILL_TOOLBAR,
                },
            });

            quill.setContents(initialContent);

            quill.on('text-change', () => {
                this.alpUnsaved = this.alpHasUnsavedChanges();
            });

            // Expose the raw instance for tests. DOM elements are never wrapped
            // by Alpine's reactivity, so this handle stays proxy-free.
            this.$el._alpQuill = quill;

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
            if (! quill) return false;

            const title = this.$refs.alpTitleInput.value;
            const content = JSON.stringify(quill.getContents());

            return title !== lastSyncedTitle || content !== lastSyncedContent;
        },

        alpTick() {
            this.alpUnsaved = this.alpHasUnsavedChanges();

            if (! this.alpUnsaved || this.alpSyncing) return;

            this.alpSync();
        },

        async alpSync({ keepalive = false } = {}) {
            if (! quill || this.alpSyncing) return;

            // Snapshot before fetch so edits made during flight stay marked unsaved.
            const title = this.$refs.alpTitleInput.value;
            const content = JSON.stringify(quill.getContents());

            this.alpSyncing = true;
            this.alpSaveState = 'saving';

            const result = await bffPut(syncUrl, {
                title: title,
                content: JSON.parse(content),
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
