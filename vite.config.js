import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import '@fortawesome/fontawesome-free';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                '@fortawesome/fontawesome-free/css/' // add the Font Awesome CSS file here
            ],
            refresh: true,
        }),
    ],
});
