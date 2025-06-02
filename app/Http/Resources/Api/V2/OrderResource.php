<?php

namespace App\Http\Resources\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'reference' => $this->reference,
            'payment_method' => $this->payment_method === "Payment on delivery (POD)" ? "الدفع الإلكتروني عند الإستلام" : $this->payment_method,
            'order_status_id' => $this->order_status_id,
            'status' => $this->orderStatus->name,
            'status_ar' => $this->orderStatus->name_ar,
            'delivery_date' => \Carbon\Carbon::parse($this->delivery_date)->format('Y-m-d'),
            'start_time' => \Carbon\Carbon::parse($this->start_time)->format('H:i'),
            'end_time' => \Carbon\Carbon::parse($this->end_time)->format('H:i'),
            'price' => $this->price,
            'total_paid' => $this->total_paid,
            'total_shipping' => $this->total_shipping,
            'customer_name' => $this->customer_name,
            'address' => $this->address,
            'customer_phone' => $this->customer_phone,
            'items' => $this->items,
            'latitude' => $this->location->latitude,
            'longitude' => $this->location->longitude,
            'products_by_seller' => $productsBySeller->map(function ($products, $sellerName) {
                $seller = $products[0]['details']['seller'] ?? [];
                return [
                    'seller_name' => $sellerName,
                    'seller_logo' => $seller['logo'] ?? '',
                    'seller_phone' => $seller['phone'] ?? '',
                    'seller_location' => $products[0]['details']['seller_location'] ?? '',
                    'jm_order_id' => $products[0]['jm_order_id'] ?? '',
                    'products' => collect($products)->map(function ($product) {
                        return [
                            'name' => $product['name'],
                            'image' => $product['details']['images'][0]['src'] ?? '',
                            'price' => $product['details']['price'] ?? '',
                            'description' => $product['details']['description'] ?? '',
                            'quantity' => $product['quantity'],
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }
}
