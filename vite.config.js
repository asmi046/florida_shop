import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'public/css/main.css',
                'public/css/tovar_filter.css',
                'public/css/tovar_page_content.css',
                'public/css/catalog_menu.css',
                'public/css/mobile_bottom_menu.css',


                'resources/js/app.js',
                'public/js/sliders.js',
                'public/js/delivery_zone.js',
                'public/js/filter.js',
                'public/js/map.js',
                'public/js/categories.js',
                'public/js/catalog_menu.js'
            ],
            refresh: true,
        }),
    ],
});
