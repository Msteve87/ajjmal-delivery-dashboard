<div class="p-4 space-y-4">
    <p><strong>Reference #:</strong> {{ $record->reference }}</p>

    @php
        // Group products by seller name
        $productsBySeller = collect($record->products)->groupBy(function ($product) {
            return $product['details']['seller']['name'] ?? 'بدون بائع';
        });
    @endphp

    @foreach($productsBySeller as $sellerName => $products)
        <div class="mb-8 border border-gray-300 rounded-lg shadow-sm bg-gray-50">
            <!-- Seller Info -->
            <div class="flex items-center p-4 border-b">
                <img src="{{ $products[0]['details']['seller']['logo'] ?? '' }}" alt="{{ $sellerName }}"
                    class="w-12 h-12 rounded-full border object-cover">
                <div class="mr-3">
                    <h4 class="text-base font-semibold">{{ $sellerName }}</h4>
                    <a href="tel:{{ $products[0]['details']['seller']['phone'] ?? '' }}" class="text-xs text-blue-500">
                        {{ $products[0]['details']['seller']['phone'] ?? '' }}
                    </a>
                </div>
            </div>
            <!-- Products List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                @foreach($products as $product)
                    <div class="max-w-xs bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                        <!-- Product Image -->
                        <div class="h-48 bg-gray-100 overflow-hidden">
                            <img src="{{ $product['details']['images'][0]['src'] ?? '' }}" alt="{{ $product['name'] }}"
                                class="w-full h-full object-cover">
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <!-- Title & Price -->
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $product['name'] }}</h3>
                                <span class="text-lg font-bold text-blue-600">{{ $product['details']['price'] ?? '' }}</span>
                            </div>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-3">{{ $product['details']['description'] ?? '' }}</p>

                            <!-- Quantity -->
                            <div class="text-sm text-gray-700 mb-3">
                                الكمية: <span class="font-medium">{{ $product['quantity'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</div>

<!-- "products": [
                    {
                        "name": "قراديلا متوسطة",
                        "price": "30.000000",
                        "details": {
                            "name": "قراديلا متوسطة",
                            "price": "30.00 د.ل.‏",
                            "images": [
                                {
                                    "src": "https://ajjmal.ly/10002-large_default/--6563.jpg"
                                }
                            ],
                            "seller": {
                                "logo": "https://ajjmal.ly/img/mp_seller/zondsilxwnha.jpeg",
                                "name": "عروض ومواسم",
                                "phone": "+218920000000"
                            },
                            "description": "قراديلا متوسطة ،مصنوعة من الفولاذ المقاوم للصدأ .",
                            "seller_location": {
                                "latitude": "0.00000000",
                                "longitude": "0.00000000"
                            }
                        },
                        "quantity": 1,
                        "product_id": 6563
                    }
                ], -->