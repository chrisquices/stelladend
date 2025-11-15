import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import FloatingVue from 'floating-vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import 'floating-vue/dist/style.css'
import { Toaster, toast } from 'vue-sonner'
import 'vue-sonner/style.css'
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import 'floating-vue/dist/style.css'
import VChart from 'vue-echarts';
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart } from 'echarts/charts';
import { PieChart } from 'echarts/charts';
import { LineChart } from 'echarts/charts';
import {
    TitleComponent,
    TooltipComponent,
    LegendComponent,
    GridComponent,
    DatasetComponent,
    ToolboxComponent,
} from 'echarts/components';
import { LabelLayout } from 'echarts/features';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });

        // Register required ECharts modules globally
        use([
            CanvasRenderer,
            BarChart,
            PieChart,
            LineChart,
            TitleComponent,
            TooltipComponent,
            LegendComponent,
            GridComponent,
            DatasetComponent,
            ToolboxComponent,
            LabelLayout,
        ]);

        vueApp
            .use(plugin)
            .use(ZiggyVue)
            .use(FloatingVue)
            .component('VueDatePicker', VueDatePicker)
            .component('v-chart', VChart)
            .mount(el);
    },
    progress: {
        color: cssVar('--primary'),
    },
});

export function cssVar(name: string): string {
    return getComputedStyle(document.documentElement)
        .getPropertyValue(name)
        .trim();
}

// This will set light / dark mode on page load...
// initializeTheme();
