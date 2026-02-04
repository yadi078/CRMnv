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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Paleta de colores C&CE Consultoría
                'azul-fuerte': '#003366',
                'azul': '#000836',
                'azul-bright': '#000099',
                'amarillo': '#FFFF00',
                'gris': '#808080',
            },
        },
    },

    plugins: [forms],
};
