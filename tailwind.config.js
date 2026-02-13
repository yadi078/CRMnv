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
                // Paleta corporativa Enterprise SaaS
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
