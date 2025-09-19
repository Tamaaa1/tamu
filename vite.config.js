import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom.css',
                'resources/css/admin-participants.css',
                'resources/js/app.js',
                'resources/js/admin-participants-filter.js',
                'resources/js/admin-participants-index.js',
                'resources/js/admin-agenda-index.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
