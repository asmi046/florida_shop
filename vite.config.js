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

                'resources/js/app.js',
                'public/js/sliders.js',
                'public/js/delivery_zone.js'
            ],
            refresh: true,
        }),
    ],
});
