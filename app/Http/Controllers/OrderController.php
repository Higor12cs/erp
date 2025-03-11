<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Order::with(['customer'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        return Inertia::render('Orders/Create', [
            'order' => null,
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        $this->orderService->createOrder($request->validated());

        return to_route('orders.index')->with('success', 'Pedido criado com sucesso.');
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'createdBy']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order)
    {
        $order->load(['items.product', 'createdBy']);

        return Inertia::render('Orders/Edit', [
            'order' => $order,
        ]);
    }

    public function update(OrderUpdateRequest $request, Order $order)
    {
        $this->orderService->updateOrder($order, $request->validated());

        return to_route('orders.index')->with('success', 'Pedido atualizado com sucesso.');
    }

    public function destroy(Order $order)
    {
        $this->orderService->deleteOrder($order);

        return to_route('orders.index')
            ->with('success', 'Pedido excluído com sucesso.');
    }

    public function search(Request $request)
    {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $orders = Order::whereIn('id', $ids)->get();

            return response()->json([
                'data' => $orders->map(function (Order $order) {
                    return [
                        'id' => $order->id,
                        'name' => 'Pedido #' . $order->sequential_id . ' - ' . $order->issue_date->format('d/m/Y'),
                        'total' => $order->total_price,
                    ];
                }),
            ]);
        }

        $query = $request->search ?? '';
        $orders = Order::where('sequential_id', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json([
            'data' => $orders->map(function (Order $order) {
                return [
                    'id' => $order->id,
                    'name' => 'Pedido #' . $order->sequential_id . ' - ' . $order->issue_date->format('d/m/Y'),
                    'total' => $order->total_price,
                ];
            }),
        ]);
    }
}
