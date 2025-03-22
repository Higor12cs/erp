<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            'dashboard' => [
                'name' => 'Dashboard',
                'permissions' => ['index'],
            ],

            'customers' => [
                'name' => 'Clientes',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'suppliers' => [
                'name' => 'Fornecedores',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'orders' => [
                'name' => 'Pedidos',
                'permissions' => ['index', 'create', 'edit', 'destroy', 'create-receivables'],
            ],

            'purchases' => [
                'name' => 'Compras',
                'permissions' => ['index', 'create', 'edit', 'destroy', 'create-payables'],
            ],

            'receivables' => [
                'name' => 'Recebíveis',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'payables' => [
                'name' => 'Pagáveis',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'accounts' => [
                'name' => 'Contas',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'payment_methods' => [
                'name' => 'Métodos de Pagamento',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'products' => [
                'name' => 'Produtos',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'stocks' => [
                'name' => 'Estoque',
                'permissions' => ['index', 'adjust'],
            ],

            'kardex' => [
                'name' => 'Kardex',
                'permissions' => ['index'],
            ],

            'sections' => [
                'name' => 'Seções',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'groups' => [
                'name' => 'Grupos',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'brands' => [
                'name' => 'Marcas',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'users' => [
                'name' => 'Usuários',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'roles' => [
                'name' => 'Permissões',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'sellers' => [
                'name' => 'Vendedores',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'account_plans' => [
                'name' => 'Planos de Contas',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],
        ];

        $actions = [
            'index' => 'Visualizar',
            'create' => 'Criar',
            'edit' => 'Editar',
            'destroy' => 'Excluir',
            'create-receivables' => 'Recebíveis',
            'create-payables' => 'Pagáveis',
            'adjust' => 'Ajustar',
        ];

        foreach ($resources as $resource => $config) {
            foreach ($config['permissions'] as $permission) {
                Permission::firstOrCreate([
                    'name' => "$resource.$permission",
                    'description' => "{$actions[$permission]} {$config['name']}",
                ]);
            }
        }
    }
}
