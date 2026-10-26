<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Products with normal stock and varied tax rates
        Product::factory()->create([
            'name'           => 'Wireless Bluetooth Headphones',
            'code'           => 'PROD-001',
            'price'          => 2499.99,
            'tax_percentage' => 18.00,
            'stock_quantity' => 50,
        ]);

        Product::factory()->create([
            'name'           => 'USB-C Laptop Charger',
            'code'           => 'PROD-002',
            'price'          => 1299.00,
            'tax_percentage' => 12.00,
            'stock_quantity' => 75,
        ]);

        Product::factory()->create([
            'name'           => 'Mechanical Keyboard',
            'code'           => 'PROD-003',
            'price'          => 3499.50,
            'tax_percentage' => 18.00,
            'stock_quantity' => 30,
        ]);

        Product::factory()->create([
            'name'           => 'Ergonomic Mouse',
            'code'           => 'PROD-004',
            'price'          => 899.00,
            'tax_percentage' => 12.00,
            'stock_quantity' => 120,
        ]);

        Product::factory()->create([
            'name'           => 'Monitor Stand',
            'code'           => 'PROD-005',
            'price'          => 599.00,
            'tax_percentage' => 5.00,
            'stock_quantity' => 200,
        ]);

        // Products with low stock (below default threshold of 10)
        Product::factory()->create([
            'name'           => 'Webcam HD 1080p',
            'code'           => 'PROD-006',
            'price'          => 1999.00,
            'tax_percentage' => 18.00,
            'stock_quantity' => 3,
        ]);

        Product::factory()->create([
            'name'           => 'Laptop Stand Aluminium',
            'code'           => 'PROD-007',
            'price'          => 749.00,
            'tax_percentage' => 5.00,
            'stock_quantity' => 7,
        ]);

        Product::factory()->create([
            'name'           => 'USB Hub 7-Port',
            'code'           => 'PROD-008',
            'price'          => 449.00,
            'tax_percentage' => 12.00,
            'stock_quantity' => 1,
        ]);

        // Product with zero stock
        Product::factory()->create([
            'name'           => 'Discontinued Cable Set',
            'code'           => 'PROD-009',
            'price'          => 199.00,
            'tax_percentage' => 5.00,
            'stock_quantity' => 0,
        ]);

        // Additional random products
        Product::factory()->count(5)->inStock()->create();

        $this->command->info('Products seeded successfully.');
    }
}
