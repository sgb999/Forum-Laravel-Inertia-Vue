<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Category::factory(10)->create();
    }
}
