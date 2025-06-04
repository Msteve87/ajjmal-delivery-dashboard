<div>
    <h2 class="text-lg font-medium">Assign Delivery Task to {{ $driver->name }}</h2>


    <div class="mt-4">
        <label for="order" class="block text-sm font-medium text-gray-700">Select Order</label>
        <select id="order" name="order"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Select an Order --</option>

            @foreach($orders as $order)
                <option value="{{ $order->id }}" data-reference="{{ strtolower($order->reference) }}">
                    {{ $order->reference }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- <div class="mt-4">
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea id="notes" name="notes" rows="3"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div> -->

    <!-- <div class="mt-4">
        <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Assign Task
        </button>
    </div> -->
</div>