<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReceivablePaymentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rotas de redirecionamento e homepage
Route::redirect('/', '/home');

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', LoginController::class)->name('login.attempt');
    Route::get('/registrar', fn () => Inertia::render('Auth/Register'))->name('register');
    Route::post('/registrar', RegisterController::class)->name('register.attempt');
});

Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Páginas principais
    Route::get('/home', fn () => Inertia::render('Home/Index'))->name('home.index');
    Route::get('/dashboard', fn () => Inertia::render('Dashboard/Index'))->name('dashboard.index');

    // Rotas de API
    Route::prefix('/api')->as('api.')->group(function () {
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
        Route::get('/brands/search', [BrandController::class, 'search'])->name('brands.search');
        Route::get('/sections/search', [SectionController::class, 'search'])->name('sections.search');
        Route::get('/groups/search', [GroupController::class, 'search'])->name('groups.search');
        Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
        Route::get('/accounts/search', [AccountController::class, 'search'])->name('accounts.search');
        Route::get('/payment-methods/search', [PaymentMethodController::class, 'search'])->name('payment-methods.search');
    });

    // Rotas de pagamentos de recebíveis
    Route::get('/recebiveis/pagamentos', [ReceivablePaymentController::class, 'index'])->name('receivables.payments.index');
    Route::get('/recebiveis/pagamentos/novo', [ReceivablePaymentController::class, 'create'])->name('receivables.payments.create');
    Route::post('/recebiveis/pagamentos', [ReceivablePaymentController::class, 'store'])->name('receivables.payments.store');
    Route::get('/recebiveis/pagamentos/{payment:sequential_id}', [ReceivablePaymentController::class, 'show'])->name('receivables.payments.show');
    Route::delete('/recebiveis/pagamentos/{payment}', [ReceivablePaymentController::class, 'destroy'])->name('receivables.payments.destroy');

    // Rotas de recebíveis
    Route::get('/recebiveis', [ReceivableController::class, 'index'])->name('receivables.index');
    Route::get('/recebiveis/criar', [ReceivableController::class, 'create'])->name('receivables.create');
    Route::post('/recebiveis', [ReceivableController::class, 'store'])->name('receivables.store');
    Route::delete('/recebiveis', [ReceivableController::class, 'destroy'])->name('receivables.destroy');
    Route::get('/recebiveis/{receivable}/editar', [ReceivableController::class, 'edit'])->name('receivables.edit');
    Route::post('/recebiveis/{receivable}', [ReceivableController::class, 'update'])->name('receivables.update');

    // Clientes
    Route::get('/clientes', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/clientes/criar', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/clientes', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/clientes/{customer:sequential_id}/editar', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/clientes/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/clientes/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Fornecedores
    Route::get('/fornecedores', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/fornecedores/criar', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/fornecedores', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/fornecedores/{supplier}/editar', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/fornecedores/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/fornecedores/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Usuários
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/criar', [UserController::class, 'create'])->name('users.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Produtos
    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produtos/criar', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('products.store');
    Route::get('/produtos/{product:sequential_id}/editar', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produtos/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/produtos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Marcas
    Route::get('/marcas', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/marcas/criar', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/marcas', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/marcas/{brand:sequential_id}/editar', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/marcas/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/marcas/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    // Seções
    Route::get('/secoes', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/secoes/criar', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/secoes', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/secoes/{section:sequential_id}/editar', [SectionController::class, 'edit'])->name('sections.edit');
    Route::put('/secoes/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/secoes/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

    // Grupos
    Route::get('/grupos', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/grupos/criar', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/grupos', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/grupos/{group:sequential_id}/editar', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/grupos/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/grupos/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    // Pedidos
    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/criar', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/pedidos/{order:sequential_id}/editar', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('/pedidos/{order:sequential_id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/pedidos/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/pedidos/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/pedidos/{order:sequential_id}/recebiveis/criar', [OrderController::class, 'createReceivables'])->name('orders.create-receivables');
    Route::post('/pedidos/{order}/recebiveis', [OrderController::class, 'storeReceivables'])->name('orders.store-receivables');

    // Contas
    Route::get('/contas', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/contas/criar', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/contas', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/contas/{account:sequential_id}/editar', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/contas/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/contas/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    // Métodos de Pagamento
    Route::get('/formas-pagamento', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('/formas-pagamento/criar', [PaymentMethodController::class, 'create'])->name('payment-methods.create');
    Route::post('/formas-pagamento', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::get('/formas-pagamento/{paymentMethod:sequential_id}/editar', [PaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    Route::put('/formas-pagamento/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('/formas-pagamento/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
});
