<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()
            ->when(request('search'), function ($query, $search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('legal_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create()
    {
        return inertia('Suppliers/Create');
    }

    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());

        return to_route('suppliers.index');
    }

    // public function show(Supplier $supplier)
    // {
    //     //
    // }

    public function edit(Supplier $supplier)
    {
        return inertia('Suppliers/Edit', [
            'supplier' => $supplier,
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return to_route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        // TODO: Check if the supplier has any related data before deleting it

        abort(403, 'Forbidden');

        $supplier->delete();

        return to_route('suppliers.index');
    }
}
