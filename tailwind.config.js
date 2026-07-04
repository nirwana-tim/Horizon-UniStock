import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50:  '#fdf2f3',
                    100: '#fce7e9',
                    200: '#f7c5ca',
                    300: '#f09da5',
                    400: '#e55a68',
                    500: '#d6192c',
                    600: '#b80e20',
                    700: '#980416',
                    800: '#7a0513',
                    900: '#5c040e',
                    950: '#2e0105',
                },
            },
        },
    },

    plugins: [forms],
};
