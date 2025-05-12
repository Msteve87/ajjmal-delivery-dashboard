<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jm_order_id' => $this->faker->unique()->numberBetween(1000, 9999),
            'reference' => $this->faker->unique()->word(),
            'total_paid' => $this->faker->randomFloat(2, 10, 1000),
            'total_shipping' => $this->faker->randomFloat(2, 5, 50),
            'payment_method' => "الدفع الإلكتروني عند الإستلام",
            'order_status_id' => 3,
            'items' => 1,
            'products' => json_encode(
                [
                    [
                        'product_id' => $this->faker->numberBetween(1000, 9999),
                        'name' => $this->faker->word(),
                        'quantity' => $this->faker->numberBetween(1, 10),
                        'price' => $this->faker->randomFloat(2, 10, 1000),
                        'details' => [
                            'name' => $this->faker->word(),
                            'description' => $this->faker->sentence(),
                            'price' => $this->faker->randomFloat(2, 10, 1000),
                            'images' => [
                                [
                                    'src' => $this->faker->imageUrl(),
                                ],
                            ],
                            'seller' => [
                                'name' => $this->faker->company(),
                                'phone' => $this->faker->phoneNumber(),
                                'logo' => $this->faker->imageUrl(),
                            ],
                            'seller_location' => [
                                'latitude' => $this->faker->latitude(),
                                'longitude' => $this->faker->longitude(),
                            ],
                        ],
                    ],
                ]
            ),
            'address' => $this->faker->address(),
            'customer_name' => $this->faker->name(),
            'customer_phone' => '2189' . $this->faker->randomElement([1, 2, 3, 4]) . $this->faker->numerify('#######'),
            'driver_id' => auth()->id(),
            'location_id' => Location::first(),
        ];
    }
}
