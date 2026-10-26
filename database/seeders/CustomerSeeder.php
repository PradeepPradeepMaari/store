<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Fixed sample customers for easy testing
        Customer::factory()->create([
            'name'  => 'Surya Kumar',
            'email' => 'surya@example.com',
        ]);

        Customer::factory()->create([
            'name'  => 'Priya Sharma',
            'email' => 'priya@example.com',
        ]);

        Customer::factory()->create([
            'name'  => 'Raj Patel',
            'email' => 'raj@example.com',
        ]);

        // Additional random customers
        Customer::factory()->count(7)->create();

        $this->command->info('Customers seeded successfully.');
    }
}
