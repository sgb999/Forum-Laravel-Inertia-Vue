import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";

import { createApp, h, DefineComponent } from 'vue'
import {createInertiaApp, Link, Head, useHttp} from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import moment from "moment/moment";

await useHttp({}).get('/sanctum/csrf-cookie');

createInertiaApp({
    id: 'app',
        resolve: name => resolvePageComponent(
            `/resources/js/vue/pages/${name}.vue`,
            import.meta.glob('/resources/js/vue/pages/**/*.vue')
        ) as Promise<DefineComponent>,
    defaults: {
        visitOptions: () => {
            return { viewTransition: true }
        },
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .mixin({
                methods: {
                    route: (name: string, params: Parameters<typeof route>[1] ) => route(name, params)
                }
            })
            .use(VueSweetalert2)
            .use(plugin)
            .component('inertia-link', Link)
            .component('Head', Head);

        app.config.globalProperties.formatDate = (value: string) => {
            return moment.utc(String(value)).local().format('DD/MM/YYYY H:mm a')
        }
        app.mount(el)
    },
    title: title => `Forum - ${title}`
});

