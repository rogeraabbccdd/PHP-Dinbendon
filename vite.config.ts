import { fileURLToPath } from 'node:url'
import { quasar } from '@quasar/vite-plugin';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import checker from 'vite-plugin-checker'
import vueDevTools from 'vite-plugin-vue-devtools'

export default defineConfig({
    plugins: [
        vueDevTools({
            appendTo: 'resources/js/app.ts'
        }),
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        quasar({
            sassVariables: fileURLToPath(new URL('./resources/css/quasar-variables.sass', import.meta.url)),
        }),
        checker({
            typescript: true,
            vueTsc: true,
            eslint: {
                lintCommand: 'eslint -c ./eslint.config.js "./resources/**/*.{js,mjs,cjs,ts,vue}"',
                useFlatConfig: true,
            },
        }),
    ],
    resolve: {
        alias: {
            '$': fileURLToPath(new URL('./resources/assets', import.meta.url)),
        },
    },
});
