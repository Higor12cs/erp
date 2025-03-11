<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => Inertia::render('Auth/Login'))->name('login');
    Route::post('/login', LoginController::class)->name('login.attempt');
    Route::get('/registrar', fn() => Inertia::render('Auth/Register'))->name('register');
    Route::post('/registrar', RegisterController::class)->name('register.attempt');
});

Route::post('/logout', LogoutController::class)->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/home', fn() => Inertia::render('Home/Index'))->name('home.index');
    Route::get('/dashboard', fn() => Inertia::render('Dashboard/Index'))->name('dashboard.index');

    Route::get('/clientes', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/clientes/criar', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/clientes', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/clientes/{customer:sequential_id}/editar', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/clientes/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/clientes/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/fornecedores', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/fornecedores/criar', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/fornecedores', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/fornecedores/{supplier}/editar', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/fornecedores/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/fornecedores/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
    Route::get('/usuarios/criar', [UserController::class, 'create'])->name('users.create');
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
    Route::get('/usuarios/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::get('/produtos/criar', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produtos', [ProductController::class, 'store'])->name('products.store');
    Route::get('/produtos/{product:sequential_id}/editar', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produtos/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/produtos/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/marcas', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/marcas/criar', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/marcas', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/marcas/{brand:sequential_id}/editar', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/marcas/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/marcas/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

    Route::get('/secoes', [SectionController::class, 'index'])->name('sections.index');
    Route::get('/secoes/criar', [SectionController::class, 'create'])->name('sections.create');
    Route::post('/secoes', [SectionController::class, 'store'])->name('sections.store');
    Route::get('/secoes/{section:sequential_id}/editar', [SectionController::class, 'edit'])->name('sections.edit');
    Route::put('/secoes/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/secoes/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

    Route::get('/grupos', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/grupos/criar', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/grupos', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/grupos/{group:sequential_id}/editar', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/grupos/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/grupos/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

    Route::get('/pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/criar', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/pedidos', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/pedidos/{order:sequential_id}/editar', [OrderController::class, 'edit'])->name('orders.edit');
    Route::get('/pedidos/{order:sequential_id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/pedidos/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/pedidos/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    Route::prefix('/api')->as('api.')->group(function () {
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');

        Route::get('/brands/search', [BrandController::class, 'search'])->name('brands.search');
        Route::get('/sections/search', [SectionController::class, 'search'])->name('sections.search');
        Route::get('/groups/search', [GroupController::class, 'search'])->name('groups.search');

        Route::get('/orders/search', [OrderController::class, 'search'])->name('orders.search');
    });
});

Route::redirect('/', '/home');
