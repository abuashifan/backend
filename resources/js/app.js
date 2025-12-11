import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';

createInertiaApp({
	resolve: (name) =>
		import(`./Pages/${name}.vue`).then((module) => module.default),
	setup({ el, App, props, plugin }) {
		createApp({ render: () => h(App, props) }).use(plugin).mount(el);
	},
});
