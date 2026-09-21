import './bootstrap';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

import.meta.glob([
    '../images/**',
]);

window.Quill = Quill;
