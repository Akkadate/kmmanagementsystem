import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/tinymce-init.js'
            ],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/tinymce/skins',
                    dest: 'tinymce'
                },
                {
                    src: 'node_modules/tinymce/models',
                    dest: 'tinymce'
                }
            ]
        })
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    tinymce: ['tinymce']
                }
            }
        }
    }
});
