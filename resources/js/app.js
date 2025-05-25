import './bootstrap';
import ace from 'ace-builds'
import 'ace-builds/src-noconflict/mode-xml';
import 'ace-builds/src-noconflict/theme-monokai';

import.meta.glob([
    '../images/**',
]);

window.ace = ace;
