<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->words(3, true),
            'code'           => strtoupper(Str::random(3)) . '-' . $this->faker->unique()->numerify('####'),
            'price'          => $this->faker->randomFloat(2, 5, 500),
            'tax_percentage' => $this->faker->randomElement([0, 5, 12, 18, 28]),
            'stock_quantity' => $this->faker->numberBetween(5, 200),
        ];
    }

    /** Product with ample stock */
    public function inStock(int $quantity = 100): static
    {
        return $this->state(fn () => ['stock_quantity' => $quantity]);
    }

    /** Product with low stock (1–9) */
    public function lowStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => $this->faker->numberBetween(1, 9)]);
    }

    /** Product out of stock */
    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => 0]);
    }

    /** Product with a specific price */
    public function withPrice(float $price): static
    {
        return $this->state(fn () => ['price' => $price]);
    }

    /** Product with a specific tax percentage */
    public function withTax(float $taxPercentage): static
    {
        return $this->state(fn () => ['tax_percentage' => $taxPercentage]);
    }
}
