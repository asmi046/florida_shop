import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'public/css/main.scss',
                'public/fonts/icons/style.css',
                'public/css/tovar_filter.css',
                'public/css/tovar_page_content.css',
                'public/css/catalog_menu.css',
                'public/css/cart.css',
                'public/css/auth.css',
                'public/css/cabinet.css',
                'public/css/mainsearch.css',
                'public/css/pagination.css',


                'resources/js/app.js',
                'public/js/sliders.js',
                'public/js/delivery_zone.js',
                'public/js/filter.js',
                'public/js/map.js',
                'public/js/categories.js',
                'public/js/catalog_menu.js',
                'public/js/cart.js',
                'public/js/favorites.js',
                'public/js/mainsearch.js',
            ],
            refresh: true,
        }),
    ],
});
