<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $categoryWork = Category::where('name', 'Work')->first();
        $priorityHigh = Priority::where('name', 'High')->first();
        $statusOpen = Status::where('name', 'Open')->first();

        Task::create([
            'name' => 'Complete Report',
            'description' => 'Finish the quarterly report for the company.',
            'deadline' => now()->addDays(3),
            'estimated_time' => '120',
            'effective_time' => '90',
            'user_id' => $user->id,
            'category_id' => $categoryWork->id,
            'priority_id' => $priorityHigh->id,
            'status_id' => $statusOpen->id,
        ]);
    }
}


