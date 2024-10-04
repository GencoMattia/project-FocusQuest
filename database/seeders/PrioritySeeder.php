<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Priority;

class PrioritySeeder extends Seeder
{
    public function run()
    {
        Priority::create([
            'name' => 'High',
            'color' => '#FF0000',
            'level' => 1,
            'description' => 'High priority tasks',
        ]);

        Priority::create([
            'name' => 'Medium',
            'color' => '#FFFF00',
            'level' => 2,
            'description' => 'Medium priority tasks',
        ]);

        Priority::create([
            'name' => 'Low',
            'color' => '#00FF00',
            'level' => 3,
            'description' => 'Low priority tasks',
        ]);
    }
}
