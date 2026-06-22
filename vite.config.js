import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/email-template-editor.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'efgtrack.test',
        port: 5173,
    },
});
