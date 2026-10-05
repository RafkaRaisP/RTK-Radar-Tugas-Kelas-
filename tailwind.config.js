import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'putih-kertas': '#F9F9F9',
                'merah-elegan': '#C8102E',
                'hitam-pekat': '#0D0D0D',
                'hijau-rimba': '#0B3B24',
                'hijau-aksen': '#1B5E20',
            },
        },
    },
    plugins: [],
};
