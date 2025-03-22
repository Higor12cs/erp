<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRequest;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::query()
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return inertia('Sellers/Index', [
            'sellers' => $sellers,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create()
    {
        return inertia('Sellers/Create');
    }

    public function store(SellerRequest $request)
    {
        Seller::create($request->validated());

        return to_route('sellers.index');
    }

    // public function show(Seller $seller)
    // {
    //     //
    // }

    public function edit(Seller $seller)
    {
        return inertia('Sellers/Edit', [
            'seller' => $seller,
        ]);
    }

    public function update(SellerRequest $request, Seller $seller)
    {
        $seller->update($request->validated());

        return to_route('sellers.index');
    }

    public function destroy(Seller $seller)
    {
        // TODO: Check if the seller has any related data before deleting it

        abort(403, 'Forbidden');

        $seller->delete();

        return to_route('sellers.index');
    }

    public function search(Request $request)
    {
        $query = $request->input('search', '');
        $ids = $request->input('ids');

        if ($ids) {
            return Seller::whereIn('id', explode(',', $ids))
                ->take(10)
                ->get(['id', 'name']);
        }

        return Seller::where('name', 'like', "%{$query}%")
            ->take(10)
            ->get(['id', 'name']);
    }
}
