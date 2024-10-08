<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Moment;
use App\Models\Emotion;
use App\Models\MomentsType;

class MomentSeeder extends Seeder
{
    public function run()
    {
        $emotionHappiness = Emotion::where('name', 'Happiness')->first();
        $emotionSadness = Emotion::where('name', 'Sadness')->first();
        $momentType = MomentsType::first();

        Moment::create([
            'name' => 'Morning Walk',
            'message' => 'Had a pleasant walk in the park.',
            'started_at' => now()->subHours(2),
            'ended_at' => now()->subHour(1),
            'emotion_id' => $emotionHappiness->id,
            'moments_type_id' => $momentType->id,
        ]);

        Moment::create([
            'name' => 'Missed Deadline',
            'message' => 'Failed to submit work on time.',
            'started_at' => now()->subDays(1),
            'ended_at' => now(),
            'emotion_id' => $emotionSadness->id,
            'moments_type_id' => $momentType->id,
        ]);
    }
}
