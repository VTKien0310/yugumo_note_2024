import './bootstrap';

import.meta.glob([
    '../images/**',
]);

import '@toast-ui/editor/dist/toastui-editor.css';
import {Editor} from "@toast-ui/editor";

const editor = new Editor({
    el: document.querySelector('#editor'),
    height: '500px',
    initialEditType: 'wysiwyg',
    previewStyle: 'vertical'
});
