import { defineFlatConfig } from 'eslint-define-config';
import vue from 'eslint-plugin-vue';
import prettier from 'eslint-config-prettier';

export default defineFlatConfig({
    ignores: [
        'vendor',
        'node_modules',
        'public',
        'bootstrap/ssr',
        'tailwind.config.js',
        'resources/js/components/ui/*'
    ],
    languageOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module'
    },
    plugins: {
        vue
    },
    rules: {
        ...vue.configs['flat/essential'].rules,
        'vue/multi-word-component-names': 'off'
    },
    extends: [prettier]
});
