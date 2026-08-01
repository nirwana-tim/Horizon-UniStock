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
                    DEFAULT: '#980416',
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
                'primary-container': '#800012',
                'on-primary': '#ffffff',
                'on-primary-container': '#ffa09a',
                secondary: {
                    DEFAULT: '#5f5e5e',
                    container: '#e4e2e1',
                },
                'on-secondary': '#ffffff',
                'on-secondary-container': '#656464',
                tertiary: {
                    DEFAULT: '#323434',
                    container: '#484a4a',
                },
                'on-tertiary': '#ffffff',
                'on-tertiary-container': '#b9b9ba',
                error: {
                    DEFAULT: '#ba1a1a',
                    container: '#ffdad6',
                },
                'on-error': '#ffffff',
                'on-error-container': '#93000a',
                background: '#fbf9f8',
                'on-background': '#1b1c1c',
                surface: {
                    DEFAULT: '#fbf9f8',
                    variant: '#e4e2e2',
                    dim: '#dbdad9',
                    bright: '#fbf9f8',
                    tint: '#b62228',
                    container: {
                        DEFAULT: '#efeded',
                        lowest: '#ffffff',
                        low: '#f5f3f3',
                        high: '#e9e8e7',
                        highest: '#e4e2e2',
                    },
                },
                'on-surface': {
                    DEFAULT: '#1b1c1c',
                    variant: '#5a403e',
                },
                outline: {
                    DEFAULT: '#8e706d',
                    variant: '#e3bebb',
                },
                'inverse-surface': '#303031',
                'inverse-on-surface': '#f2f0f0',
            },
            borderRadius: {
                DEFAULT: '0.25rem',
                lg: '0.5rem',
                xl: '0.75rem',
                '2xl': '1rem',
                full: '9999px',
            },
            spacing: {
                gutter: '16px',
                'stack-sm': '8px',
                'container-margin': '20px',
                'stack-lg': '24px',
                'section-padding': '32px',
                'stack-md': '16px',
            },
            fontSize: {
                'headline-lg-mobile': ['24px', { lineHeight: '30px', fontWeight: '700' }],
                'label-md': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '600' }],
                'headline-lg': ['28px', { lineHeight: '34px', letterSpacing: '-0.02em', fontWeight: '700' }],
                'body-md': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'headline-md': ['22px', { lineHeight: '28px', letterSpacing: '-0.01em', fontWeight: '600' }],
                'headline-sm': ['18px', { lineHeight: '24px', fontWeight: '600' }],
            },
        },
    },

    plugins: [forms],
};
