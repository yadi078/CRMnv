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
        screens: {
            xs: '320px',
            sm: '481px',
            md: '769px',
            lg: '1025px',
            xl: '1441px',
            '2xl': '1920px',
        },
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'fluid-3xs': 'clamp(0.6875rem, 0.6rem + 0.35vw, 0.75rem)',
                'fluid-xs': 'clamp(0.75rem, 0.68rem + 0.35vw, 0.8125rem)',
                'fluid-sm': 'clamp(0.8125rem, 2vw + 0.7rem, 0.875rem)',
                'fluid-base': 'clamp(0.875rem, 2vw + 0.75rem, 1rem)',
                'fluid-lg': 'clamp(1rem, 2vw + 0.85rem, 1.125rem)',
                'fluid-xl': 'clamp(1.125rem, 2vw + 0.95rem, 1.25rem)',
                'fluid-2xl': 'clamp(1.25rem, 3vw + 1rem, 1.5rem)',
                'fluid-3xl': 'clamp(1.375rem, 2.5vw + 1rem, 1.75rem)',
            },
            spacing: {
                'touch': '2.75rem',
                '18': '4.5rem',
            },
            minHeight: {
                'touch': '44px',
            },
            colors: {
                'azul-fuerte': '#003366',
                'azul': '#000836',
                'azul-bright': '#003366',
                'amarillo': '#FFE600',
                'fondo': '#F4F7FB',
                'fondo-top': '#F8FAFC',
                'fondo-bottom': '#EEF2F7',
                'borde': '#E2E8F0',
                'texto': '#1F2937',
                'texto-sec': '#6B7280',
            },
        },
    },

    plugins: [forms],
};
