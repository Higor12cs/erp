export const sidebarItems = [
    // Início
    {
        type: "header",
        label: "Início",
        items: [
            {
                type: "link",
                routeName: "home.index",
                iconClass: "fas fa-home",
                label: "Home",
            },
            {
                type: "link",
                routeName: "dashboard.index",
                iconClass: "fas fa-tachometer-alt",
                label: "Dashboard",
                permission: "dashboard.index",
            },
        ],
    },

    // Cadastros
    {
        type: "header",
        label: "Cadastros",
        items: [
            {
                type: "link",
                routeName: "customers.index",
                iconClass: "fas fa-users",
                label: "Clientes",
                permission: "customers.index",
            },
            {
                type: "link",
                routeName: "suppliers.index",
                iconClass: "fas fa-users",
                label: "Fornecedores",
                permission: "suppliers.index",
            },
        ],
    },

    // Vendas
    {
        type: "header",
        label: "Vendas",
        items: [
            {
                type: "collapsible",
                iconClass: "fas fa-shopping-basket",
                label: "Pedidos",
                subItems: [
                    {
                        type: "link",
                        routeName: "orders.index",
                        iconClass: "fas fa-shopping-basket",
                        label: "Pedidos",
                        permission: "orders.index",
                    },
                    {
                        type: "link",
                        routeName: "orders.create",
                        iconClass: "fas fa-shopping-basket",
                        label: "Novo Pedido",
                        permission: "orders.create",
                    },
                ],
            },
        ],
    },

    // Compras
    {
        type: "header",
        label: "Compras",
        items: [
            {
                type: "collapsible",
                iconClass: "fas fa-truck-loading",
                label: "Compras",
                subItems: [
                    {
                        routeName: "purchases.index",
                        iconClass: "fas fa-truck-loading",
                        label: "Compras",
                        permission: "purchases.index",
                    },
                    {
                        routeName: "purchases.create",
                        iconClass: "fas fa-truck-loading",
                        label: "Nova Compra",
                        permission: "purchases.create",
                    },
                ],
            },
        ],
    },

    // Financeiro
    {
        type: "header",
        label: "Financeiro",
        items: [
            {
                type: "collapsible",
                iconClass: "fas fa-university",
                label: "Financeiro",
                subItems: [
                    {
                        routeName: "receivables.index",
                        iconClass: "fas fa-arrow-down",
                        label: "Recebíveis",
                        permission: "receivables.index",
                    },
                    {
                        routeName: "payables.index",
                        iconClass: "fas fa-arrow-up",
                        label: "Pagáveis",
                        permission: "payables.index",
                    },
                    {
                        routeName: "accounts.index",
                        iconClass: "fas fa-university",
                        label: "Contas",
                        permission: "accounts.index",
                    },
                    {
                        routeName: "payment-methods.index",
                        iconClass: "fas fa-wallet",
                        label: "Métodos de Pagamento",
                        permission: "payment-methods.index",
                    },
                ],
            },
        ],
    },

    // Estoque
    {
        type: "header",
        label: "Estoque",
        items: [
            {
                type: "link",
                routeName: "products.index",
                iconClass: "fas fa-box",
                label: "Produtos",
                permission: "products.index",
            },
            {
                type: "link",
                routeName: "stocks.index",
                iconClass: "fas fa-boxes",
                label: "Estoque",
                permission: "stocks.index",
            },
            {
                type: "link",
                routeName: "kardex.index",
                iconClass: "fas fa-dolly",
                label: "Kardex",
                permission: "kardex.index",
            },
            {
                type: "collapsible",
                iconClass: "fas fa-tags",
                label: "Atributos",
                permission: "atributos.index",
                subItems: [
                    {
                        routeName: "sections.index",
                        iconClass: "fas fa-tag",
                        label: "Seções",
                        permission: "sections.index",
                    },
                    {
                        routeName: "groups.index",
                        iconClass: "fas fa-tag",
                        label: "Grupos",
                        permission: "groups.index",
                    },
                    {
                        routeName: "brands.index",
                        iconClass: "fas fa-tag",
                        label: "Marcas",
                        permission: "brands.index",
                    },
                ],
            },
        ],
    },

    // Configurações
    {
        type: "header",
        label: "Configurações",
        items: [
            {
                type: "link",
                routeName: "users.index",
                iconClass: "fas fa-users",
                label: "Usuários",
                permission: "users.index",
            },
            {
                type: "link",
                routeName: "roles.index",
                iconClass: "fas fa-lock",
                label: "Papéis",
                permission: "roles.index",
            },
        ],
    },
];

export const hasPermission = (permission, page) => {
    if (!permission) return true;

    const userRoles = page.props.auth.roles || [];
    const userPermissions = page.props.auth.permissions || [];

    if (userRoles.includes("Administrador")) return true;

    return userPermissions.includes(permission);
};
