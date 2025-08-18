<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
