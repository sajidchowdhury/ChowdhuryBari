import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Production optimizations — esbuild minifier is built-in (no extra install)
        target: 'es2020',
        minify: 'esbuild',
        rollupOptions: {
            output: {
                // Consistent file naming for cache predictability
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
            },
        },
    },
    // Disable sourcemaps in production
    css: {
        devSourcemap: false,
    },
});
