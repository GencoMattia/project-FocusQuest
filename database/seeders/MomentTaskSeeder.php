<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Moment;
use App\Models\Task;

class MomentTaskSeeder extends Seeder
{
    public function run()
    {
        // Assumendo che ci siano già dei momenti e dei task nel database
        $moments = Moment::all();
        $tasks = Task::all();

        foreach ($tasks as $task) {
            foreach ($moments as $moment) {
                // Associa il momento al task
                $task->moments()->attach($moment->id);
            }
        }
    }

}
