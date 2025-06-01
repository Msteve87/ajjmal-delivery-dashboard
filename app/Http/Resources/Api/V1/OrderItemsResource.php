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
        // Parse this
        //          "delivery_date" => "2025-05-29"
//     "start_time" => "09:00"
//     "end_time" => "10:00"
//     "reference" => "GIKSTEHBM"
//     "payment_method" => "الدفع عند الاستلام"
//     "total_paid" => "155.00"
//     "total_shipping" => "10.00"
//     "status" => "pending"
//     "status_ar" => "جديدة"
//     "current_state_name" => "Processing in progress"
//     "customer_name" => "Ajjmal User"
//     "address" => "يدر"
//     "customer_phone" => "0910057059"
//     "latitude" => "32.399504428658"
//     "longitude" => "15.125762231986"
//     "products" => array:1 [
//       0 => array:6 [
//         "product_id" => 11177
//         "name" => "عطر خمرة قهوة"
//         "quantity" => 1
//         "price" => "145.000000"
//         "details" => array:6 [
//           "name" => "عطر خمرة قهوة"
//           "description" => """
//             &amp;nbsp;عطر فاخر مميز يجمع بين الحلوة والدافئه والتوابل مع لمحة من القهوه مما يجعاه خيار مثاليا لمحبي العطور الغورماندية
//             مل100
//             """
//           "price" => "145.00\u{A0}د.ل.\u{200F}"
//           "images" => array:1 [
//             0 => array:1 [
//               "src" => "https://ajjmal.ly/18667-large_default/--11177.jpg"
//             ]
//           ]
//           "seller" => array:3 [
//             "name" => "ماريا بيوتي"
//             "phone" => "+218918683837"
//             "logo" => "https://ajjmal.ly/img/mp_seller/whrfcyqtrnpu.png"
//           ]
//           "seller_location" => array:2 [
//             "latitude" => "32.87487300"
//             "longitude" => "13.25480500"
//           ]
//         ]
//         "jm_order_id" => 3118
//       ]
//     ]
//     "items" => 1
//   ]
        // to return this
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
            'items' => $this['items'],
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
