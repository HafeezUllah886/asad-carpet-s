<?php

namespace Database\Seeders;

use App\Models\products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class productsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['code' => "3420", 'color' => "Gray", "desc" => "Test Product 1", "length" => 15, "width" => 4, 'price' => 270, "cat" => "Carpet"],
            ['code' => "3423", 'color' => "Green", "desc" => "Test Product 2", "length" => 10, "width" => 4, 'price' => 280, "cat" => "Carpet"],
            ['code' => "3424", 'color' => "Blue", "desc" => "Test Product 3", "length" => 25, "width" => 4, 'price' => 34000, "cat" => "Kaleen"],
        ];
        products::insert($data);
    }
}
