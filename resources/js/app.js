import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import jQuery from "jquery";
window.$ = window.jQuery = jQuery;
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "admin-lte/dist/js/adminlte.min.js";
import Select2 from "select2";
Select2($);
import "../css/app.css";
import "icheck-bootstrap/icheck-bootstrap.min.css";
import "select2/dist/css/select2.css";
import "@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

window.initAdminLTE = function () {
    if (window.$ && window.$.AdminLTE) {
        if (!window.adminLTEInitialized) {
            window.$.AdminLTE.init();
            window.adminLTEInitialized = true;
        }
    }

    if (window.$ && window.$.fn.Treeview) {
        const treeViewElements = $('[data-widget="treeview"]').not('.treeview-initialized');
        if (treeViewElements.length) {
            treeViewElements.Treeview('init').addClass('treeview-initialized');
        }
    }

    if (window.$ && window.$.fn.PushMenu) {
        const pushMenuElements = $('[data-widget="pushmenu"]').not('.pushmenu-initialized');
        if (pushMenuElements.length) {
            pushMenuElements.PushMenu('init').addClass('pushmenu-initialized');
        }
    }

    if (window.$ && window.$.fn.Layout) {
        const layoutElements = $('[data-widget="layout"]').not('.layout-initialized');
        if (layoutElements.length) {
            layoutElements.Layout('init').addClass('layout-initialized');
        }
    }
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        app.mount(el);

        setTimeout(() => {
            if (window.initAdminLTE) {
                window.initAdminLTE();
            }
        }, 500);

        return app;
    },
    progress: {
        color: "#007BFF",
    },
});

document.addEventListener('inertia:finish', () => {
    setTimeout(() => {
        if (window.initAdminLTE) {
            window.initAdminLTE();
        }
    }, 100);
});
