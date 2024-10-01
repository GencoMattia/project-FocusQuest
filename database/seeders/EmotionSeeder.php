<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emotion;

class EmotionSeeder extends Seeder
{
    public function run()
    {
        Emotion::create([
            'name' => 'Happiness',
            'icon' => '🙂',
            'color' => '#FFD700',
        ]);

        Emotion::create([
            'name' => 'Sadness',
            'icon' => '😢',
            'color' => '#1E90FF',
        ]);
    }
}
