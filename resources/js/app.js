import './bootstrap';
import '../css/app.css';

// Font Inter di-host sendiri — bukan dari Google Fonts.
// Selain menghapus permintaan jaringan yang memblokir render, ini juga
// syarat agar tipografi tetap benar saat aplikasi dipakai offline (PWA, P6).
import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { registerPwaServiceWorker } from './pwa';

// Register PWA Service Worker
registerPwaServiceWorker();

createInertiaApp({
    title: (title) => title ? `${title} - SiPinjam` : 'SiPinjam',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        return pages[`./Pages/${name}.vue`];
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#3b82f6',
        showSpinner: true,
    },
});
