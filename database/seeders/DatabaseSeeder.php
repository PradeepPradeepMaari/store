<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
