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
        window.$.AdminLTE.init();
    }

    if (window.$ && window.$.fn.Treeview) {
        $('[data-widget="treeview"]').Treeview('init');
    }

    if (window.$ && window.$.fn.PushMenu) {
        $('[data-widget="pushmenu"]').PushMenu('init');
    }

    if (window.$ && window.$.fn.Layout) {
        $('[data-widget="layout"]').Layout('init');
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
