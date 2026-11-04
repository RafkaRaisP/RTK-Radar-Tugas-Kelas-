import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                // Diredam agar lolos WCAG 4.5:1 di atas surface terang
                // (#F5EFE3 notebook / #FFFDF7 kertas). Nilai default Tailwind
                // stone-400 #a8a29e = 2.2:1 dan stone-500 #78716c = 4.19:1 — gagal.
                stone: {
                    400: '#6B655D', // 5.03 notebook / 5.66 kertas
                    500: '#57534E', // 6.66 notebook / 7.50 kertas
                },
            },
        },
    },
    plugins: [],
};
