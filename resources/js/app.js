import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { ProCalendar } from "vue-pro-calendar";
import VCalendar from 'v-calendar';
import mitt from 'mitt';

const emitter = mitt();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue)
        .use(ProCalendar)
        .use(VCalendar);
        
        app.config.globalProperties.emitter = emitter;
        
        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
