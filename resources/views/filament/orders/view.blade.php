<div class="p-4 space-y-4">
    <h2 class="text-lg font-bold">Order #{{ $record->jm_order_id }}</h2>
    <p><strong>Reference:</strong> {{ $record->reference }}</p>
    <!-- <p><strong>Payment Method:</strong> {{ $record->payment_method }}</p>
    <p><strong>Customer Name:</strong> {{ $record->customer_name }}</p>
    <p><strong>Driver Name:</strong> {{ $record->driver->first_name ?? 'N/A' }}</p>
    <p><strong>Status:</strong> {{ $record->orderStatus->slug }}</p>
    <p><strong>Total Shipping:</strong> {{ $record->total_shipping }}</p>
    <p><strong>Total Paid:</strong> {{ $record->total_paid }}</p> -->

    <h3 class="text-md font-semibold mt-4">Products</h3>
    <ul class="list-disc pl-5">
        @foreach($record->products as $product)
            <li>{{ $product['name'] }} - {{ $product['quantity'] }} x {{ $product['price'] }}</li>
        @endforeach
    </ul>
</div>