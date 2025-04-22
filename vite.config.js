import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/main.jsx', 'resources/css/index.css'],
            refresh: true,
            buildDirectory: 'build',
        }),
        react(),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                main: 'resources/js/main.jsx',
            },
            output: {
                entryFileNames: 'assets/[name].[hash].js',
                chunkFileNames: 'assets/[name].[hash].js',
                assetFileNames: 'assets/[name].[hash][extname]',
            },
        },
    },
    css: {
        modules: {
            localsConvention: 'camelCaseOnly',
        },
    },
    server: {
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
        }
    }

})

