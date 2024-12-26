<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $sizes = [
            ['size_id' => Str::uuid(), 'size_name' => 'Small', 'created_at' => now(), 'updated_at' => now()],
            ['size_id' => Str::uuid(), 'size_name' => 'Medium', 'created_at' => now(), 'updated_at' => now()],
            ['size_id' => Str::uuid(), 'size_name' => 'Large', 'created_at' => now(), 'updated_at' => now()],
            ['size_id' => Str::uuid(), 'size_name' => 'X-Large', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('sizes')->insert($sizes);
    }
}
