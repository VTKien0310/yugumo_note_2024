import './bootstrap';
import 'quill/dist/quill.snow.css';
import advancedNoteEditor from './libs/advanced-note-editor';

import.meta.glob([
    '../images/**',
]);

// Alpine ships inside the Livewire bundle, which is loaded by a classic script
// tag before this deferred module runs - so `window.Alpine` exists here, and
// `alpine:init` has not fired yet. Importing `alpinejs` directly would register
// a second instance and trigger Livewire's duplicate-instance warning.
document.addEventListener('alpine:init', () => {
    window.Alpine.data('advancedNoteEditor', advancedNoteEditor);
});
