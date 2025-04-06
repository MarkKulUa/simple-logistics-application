// vite.config.mjs
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: ['src/main.jsx', 'src/index.css'],
            refresh: true,
        }),
        react(),
    ],
    css: {
        modules: {
            localsConvention: 'camelCaseOnly',
        },
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
    },
})
