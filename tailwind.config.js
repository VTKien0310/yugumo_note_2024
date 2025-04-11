/** @type {import('tailwindcss').Config} */
import daisyui from "daisyui";
import typography from "@tailwindcss/typography";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [
        typography,
        daisyui
    ],
    daisyui: {
        themes: ["winter"]
    }
}
