<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => 'Work',
            'color' => '#FF5733',
            'description' => 'Tasks related to work',
        ]);

        Category::create([
            'name' => 'Personal',
            'color' => '#33FF57',
            'description' => 'Personal life tasks',
        ]);
    }
}
