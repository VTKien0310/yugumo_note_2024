import './bootstrap';

import.meta.glob([
    '../images/**',
]);

import '@toast-ui/editor/dist/toastui-editor.css';
import {Editor} from "@toast-ui/editor";

let editor = new Editor({
    el: document.querySelector('#editor'),
    height: '500px',
    initialEditType: 'wysiwyg',
    previewStyle: 'vertical',
    events: {
        change: () => {
            document.querySelector('#editor').dispatchEvent(
                new CustomEvent('content-change', {
                    detail: editor.getMarkdown()
                })
            )
        }
    }
});

document.addEventListener('livewire:init', () => {
    Livewire.on('advanced-note-content-updated', (event) => {
        editor.setMarkdown(event.noteContent, true)
    });
});
