<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function calculateOrderDetails(array $items, float $orderDiscount = 0, float $orderFees = 0): array
    {
        $totalCost = 0;
        $totalPrice = 0;
        $processedItems = [];
        $existingItemIds = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $quantity = $item['quantity'];
            $unitCost = $product->cost;
            $unitPrice = $item['unit_price'];
            $itemDiscount = ($item['discount'] ?? 0);
            $itemFees = ($item['fees'] ?? 0);

            $itemTotalCost = $unitCost * $quantity;
            $itemTotalPrice = ($unitPrice * $quantity) - $itemDiscount + $itemFees;

            $totalCost += $itemTotalCost;
            $totalPrice += $itemTotalPrice;

            $itemData = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'unit_price' => $unitPrice,
                'discount' => $itemDiscount,
                'fees' => $itemFees,
                'total_cost' => $itemTotalCost,
                'total_price' => $itemTotalPrice,
                'created_by' => Auth::id(),
            ];

            if (isset($item['id'])) {
                $existingItemIds[] = $item['id'];
                $processedItems[] = array_merge(['id' => $item['id']], $itemData);
            } else {
                $processedItems[] = $itemData;
            }
        }

        $finalTotalPrice = $totalPrice - $orderDiscount + $orderFees;

        return [
            'total_cost' => $totalCost,
            'total_price' => $finalTotalPrice,
            'items' => $processedItems,
            'existing_item_ids' => $existingItemIds,
        ];
    }

    public function createOrder(array $data): Order
    {
        $orderData = $this->calculateOrderDetails(
            $data['items'],
            ($data['discount'] ?? 0),
            ($data['fees'] ?? 0)
        );

        return DB::transaction(function () use ($data, $orderData) {
            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'issue_date' => $data['issue_date'],
                'total_cost' => $orderData['total_cost'],
                'discount' => ($data['discount'] ?? 0),
                'fees' => ($data['fees'] ?? 0),
                'total_price' => $orderData['total_price'],
                'observation' => $data['observation'] ?? null,
                'created_by' => Auth::id(),
            ]);

            foreach ($orderData['items'] as $item) {
                $order->items()->create($item);
            }

            return $order;
        });
    }

    public function updateOrder(Order $order, array $data): Order
    {
        $orderData = $this->calculateOrderDetails(
            $data['items'],
            ($data['discount'] ?? 0),
            ($data['fees'] ?? 0)
        );

        return DB::transaction(function () use ($order, $data, $orderData) {
            $order->update([
                'customer_id' => $data['customer_id'],
                'issue_date' => $data['issue_date'],
                'total_cost' => $orderData['total_cost'],
                'discount' => ($data['discount'] ?? 0),
                'fees' => ($data['fees'] ?? 0),
                'total_price' => $orderData['total_price'],
                'observation' => $data['observation'] ?? null,
            ]);

            $order->items()->whereNotIn('id', $orderData['existing_item_ids'])->delete();

            foreach ($orderData['items'] as $item) {
                if (isset($item['id'])) {
                    $order->items()->where('id', $item['id'])->update($item);
                } else {
                    $order->items()->create($item);
                }
            }

            return $order->fresh(['items', 'createdBy']);
        });
    }

    public function deleteOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->delete();
        });
    }
}
