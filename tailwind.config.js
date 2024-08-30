/** @type {import('tailwindcss').Config} */
import daisyui from "daisyui";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        daisyui
    ],
    daisyui: {
        themes: ["winter"]
    }
}
