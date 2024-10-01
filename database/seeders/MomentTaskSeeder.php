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
        $moments_id = Moment::all()->pluck('id');
        $tasks_id = Task::all()->pluck('id');

        foreach ($tasks_id as $singleTask) {
            foreach ($moments_id as $singleMoment) {
                $singleTask->attach($singleMoment);
            }
        }
    }
}
