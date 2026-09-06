import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import { router } from '@inertiajs/vue3';

router.on('navigate', (event) => {
    const page = event.detail.page;
    const settings = page?.props?.pengaturan || page?.props?.settings;
    if (settings?.logo_url) {
        document.querySelectorAll("link[rel*='icon']").forEach((el) => {
            el.href = settings.logo_url;
        });
    }
});

createInertiaApp({
    title: (title) => title,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#9333ea',
    },
});
