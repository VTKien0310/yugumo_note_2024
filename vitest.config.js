import { defineConfig } from 'vitest/config';

export default defineConfig({
    test: {
        // Quill 2 requires `document.getSelection()`, `Range` and
        // `MutationObserver`. happy-dom implements all three; jsdom throws
        // "not implemented" for getSelection.
        environment: 'happy-dom',
        setupFiles: ['./tests/Js/setup.js'],
        include: ['tests/Js/**/*.test.js'],
    },
});
