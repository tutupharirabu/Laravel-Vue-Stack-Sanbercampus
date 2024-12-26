<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            ['category_id' => Str::uuid(), 'category_name' => 'Men Clothing', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => Str::uuid(), 'category_name' => 'Women Clothing', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => Str::uuid(), 'category_name' => 'Kids Clothing', 'created_at' => now(), 'updated_at' => now()],
            ['category_id' => Str::uuid(), 'category_name' => 'Accessories', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
