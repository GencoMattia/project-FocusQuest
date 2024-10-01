<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MomentsType;

class MomentsTypeSeeder extends Seeder
{
    public function run()
    {
        MomentsType::create(['name' => 'Tipologia 1']);
        MomentsType::create(['name' => 'Tipologia 2']);
        MomentsType::create(['name' => 'Tipologia 3']);
    }
}
