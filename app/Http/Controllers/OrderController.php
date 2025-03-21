<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderReceivableRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Browsershot\Browsershot;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Order::with(['customer', 'receivables'])
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
        $order = $this->orderService->createOrder($request->validated());

        // return to_route('orders.index')->with('success', 'Pedido criado com sucesso.');
        return to_route('orders.create-receivables', $order->sequential_id)
            ->with('success', 'Pedido criado com sucesso.');
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'createdBy', 'receivables.paymentMethod']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order)
    {
        if ($order->hasReceivables()) {
            return to_route('orders.show', $order->sequential_id)
                ->with('error', 'Pedidos com recebíveis não podem ser editados.');
        }

        $order->load(['items.product', 'createdBy']);

        return Inertia::render('Orders/Edit', [
            'order' => $order,
        ]);
    }

    public function update(OrderUpdateRequest $request, Order $order)
    {
        if ($order->hasReceivables()) {
            return to_route('orders.show', $order->sequential_id)
                ->with('error', 'Pedidos com recebíveis não podem ser editados.');
        }

        $this->orderService->updateOrder($order, $request->validated());

        return to_route('orders.index')->with('success', 'Pedido atualizado com sucesso.');
    }

    public function destroy(Order $order)
    {
        try {
            $this->orderService->deleteOrder($order);

            return to_route('orders.index')
                ->with('success', 'Pedido excluído com sucesso.');
        } catch (\Exception $e) {
            return to_route('orders.index')
                ->with('error', $e->getMessage());
        }
    }

    public function createReceivables(Order $order)
    {
        if ($order->hasReceivables()) {
            return to_route('orders.show', $order->sequential_id)
                ->with('error', 'Este pedido já possui recebíveis.');
        }

        return Inertia::render('Orders/CreateReceivables', [
            'order' => $order->load(['customer']),
        ]);
    }

    public function storeReceivables(OrderReceivableRequest $request, Order $order)
    {
        try {
            $receivablesData = collect($request->validated()['receivables'])
                ->map(function ($receivable) {
                    return [
                        'payment_method_id' => $receivable['payment_method_id'],
                        'due_date' => $receivable['due_date'],
                        'amount' => $receivable['amount'],
                        'description' => $receivable['description'] ?? null,
                    ];
                })
                ->toArray();

            $this->orderService->createReceivables($order, $receivablesData);

            return to_route('orders.show', $order->sequential_id)
                ->with('success', 'Recebíveis criados com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function print(Order $order, Request $request)
    {
        $type = $request->query('type', 'a4');
        if (!in_array($type, ['a4', 'thermal'])) {
            $type = 'a4';
        }

        $order = Order::with(['customer', 'items.product', 'createdBy', 'receivables.paymentMethod'])
            ->findOrFail($order->id);

        $view = $type === 'thermal' ? 'reports.order-thermal' : 'reports.order-a4';
        $html = view($view, compact('order'))->render();

        $browsershot = Browsershot::html($html)
            ->showBackground();

        if ($type === 'thermal') {
            $browsershot->paperSize(80, 297)
                ->margins(1, 1, 1, 1)
                ->deviceScaleFactor(1.5)
                ->scale(1.0);
        } else {
            $browsershot->format('A4');
        }

        $pdf = $browsershot->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline');
    }
}
