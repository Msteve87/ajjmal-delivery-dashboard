<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $productsBySeller = collect($this->products)->groupBy(function ($product) {
            return $product['details']['seller']['name'] ?? 'بدون بائع';
        });

        return [
            'id' => $this->id,
            'reference' => Order::where('id', $this->order_id)->value('reference'),
            'payment_method' => $this->order->payment_method === "Payment on delivery (POD)" ? "الدفع الإلكتروني عند الإستلام" : $this->order->payment_method,
            'tracking_id' => $this->tracking_id,
            'order_status_id' => $this->sub_order_status_id,
            'delivery_date' => $this->delivery_date ? \Carbon\Carbon::parse($this->delivery_date)->format('Y-m-d') : null,
            'start_time' => $this->start_time ? \Carbon\Carbon::parse($this->start_time)->format('H:i') : null,
            'end_time' => $this->end_time ? \Carbon\Carbon::parse($this->end_time)->format('H:i') : null,
            'status' => $this->subOrderStatus->name ?? '',
            'status_ar' => $this->subOrderStatus->name_ar ?? '',
            'price' => $this->base_price,
            'total_paid' => $this->total,
            'total_shipping' => $this->shipping_price,
            'total_discounts' => $this->total_discounts,
            'customer_name' => $this->order->customer_name,
            'address' => $this->order->address,
            'customer_phone' => $this->order->customer_phone,
            'is_picked_up' => $this->is_picked_up,
            'driver_id' => $this->driver_id,
            'date_add' => $this->date_add,
            'date_upd' => $this->date_upd,
            'latitude' => $this->order->location->latitude,
            'longitude' => $this->order->location->longitude,
            'products_by_seller' => $productsBySeller->map(function ($products, $sellerName) {
                $seller = $products[0]['details']['seller'] ?? [];
                return [
                    'seller_name' => $sellerName,
                    'seller_logo' => $seller['logo'] ?? '',
                    'seller_phone' => $seller['phone'] ?? '',
                    'seller_location' => $products[0]['details']['seller_location'] ?? '',
                    'products' => collect($products)->map(function ($product) {
                        return [
                            'product_id' => $product['product_id'],
                            'name' => $product['details']['name'] ?? $product['name'],
                            'image' => $product['details']['images'][0]['src'] ?? '',
                            'price' => $product['details']['price'] ?? $product['price'],
                            'description' => $product['details']['description'] ?? '',
                            'quantity' => $product['quantity'],
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }
}
