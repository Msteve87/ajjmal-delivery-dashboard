<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $productsBySeller = collect($this['products'])->groupBy(function ($product) {
            return $product['details']['seller']['name'] ?? 'بدون بائع';
        });

        return [
            'id' => null,
            'reference' => $this['reference'],
            'payment_method' => $this['payment_method'],
            'order_status_id' => 1,
            'status' => 'pendding',
            'status_ar' => 'جديدة',
            'delivery_date' => \Carbon\Carbon::parse($this['delivery_date'])->format('Y-m-d'),
            'start_time' => \Carbon\Carbon::parse($this['start_time'])->format('H:i'),
            'end_time' => \Carbon\Carbon::parse($this['end_time'])->format('H:i'),
            'price' => $this['total_paid'] - $this['total_shipping'],
            'total_paid' => $this['total_paid'],
            'total_shipping' => $this['total_shipping'],
            'customer_name' => $this['customer_name'],
            'address' => $this['address'],
            'customer_phone' => $this['customer_phone'],
            'latitude' => $this['latitude'],
            'longitude' => $this['longitude'],
            'items' => $this['items'],
            'products_by_seller' => $productsBySeller->map(function ($products, $sellerName) {
                $seller = $products[0]['details']['seller'] ?? [];
                return [
                    'seller_name' => $sellerName,
                    'seller_logo' => $seller['logo'] ?? '',
                    'seller_phone' => $seller['phone'] ?? '',
                    'seller_location' => $products[0]['details']['seller_location'] ?? '',
                    'jm_order_id' => $products[0]['jm_order_id'] ?? '',
                    'current_state' => $products[0]['current_state_name'],
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
