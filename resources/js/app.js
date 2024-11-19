import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura'; 
import Toast from 'primevue/toast'; 
import ToastService from 'primevue/toastservice'; 
import Dialog from 'primevue/dialog';
import DataTable from 'primevue/datatable'; 
import Column from 'primevue/column'; 

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura
                }
            })
            .use(ToastService)
            .component('Toast', Toast)  
            .component('Dialog', Dialog)
            .component('DataTable', DataTable) 
            .component('Column', Column) 
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
