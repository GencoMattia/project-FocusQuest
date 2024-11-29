<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run()
    {
        Status::create([
            'name' => 'Open',
            'color' => '#0000FF',
            'progress' => 0,
        ]);

        Status::create([
            'name' => 'In Progress',
            'color' => '#FFFF00',
            'progress' => 50,
        ]);

        Status::create([
            'name' => 'Completed',
            'color' => '#00FF00',
            'progress' => 100,
        ]);

        Status::create([
            'name' => 'Paused',
            'color' => '#65FF00',
            'progress' => 45,
        ]);
    }
}
