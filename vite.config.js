import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5174,         // puerto fijo para evitar rotación
        strictPort: true,   // no cambiar automáticamente si está ocupado
        hmr: {
            host: '127.0.0.1',
            port: 5174,
            protocol: 'ws',
        }
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
    },
    base: '/build/',
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/future-ui.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
