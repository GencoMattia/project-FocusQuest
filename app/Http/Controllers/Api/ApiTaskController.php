<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewTaskRequest;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Task;
use App\Models\Time_Interval;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class ApiTaskController extends Controller
{
    public function show($id)
    {

        $user_id = auth()->user()->id;
        $task = Task::where('user_id', '=',  $user_id)
            ->where("id", $id)
            ->with(['priority', 'status', 'category'])
            ->first();

        if ($task) {
            return response()->json([
                'message' => 'success',
                'task' => $task
            ]);
        } else {
            return response()->json([
                'message' => 'Task non trovata',
            ], 404);
        }
    }

    public function store(CreateNewTaskRequest $request)
    {
        $data = $request->validated();
        $newTask = Task::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'estimated_time' => $data['estimated_time'],
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'],
            'priority_id' => $data['priority_id'],
            'status_id' => 1,
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $newTask]);
    }

    public function getUserTask()
    {
        $authenticated_user_id = auth()->user()->id;
        $tasks = Task::with('priority')
            ->with('status')
            ->with('category')
            ->where('user_id', $authenticated_user_id)
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found for this user.'], 404);
        }

        return response()->json($tasks);
    }

    public function getFormData()
    {
        $priorities = Priority::all();
        $categories = Category::all();
        $statuses = Status::all();

        return response()->json([
            "message" => "success",
            "data" => [
                "priorities" => $priorities,
                "categories" => $categories,
                "statuses" => $statuses
            ]
        ]);
    }

    public function modifyTaskStatus(Request $request)
    {
        $data = $request->validate([
            'status_id' => 'required|integer|exists:statuses,id',
            'task_id' => 'required|integer|exists:tasks,id'
        ]);

        $task = Task::findOrFail($data['task_id']);
        $status = $data['status_id'];

        //! AVVIO
        if ($status == 2) {
            $task->started_at = now();
            $task->status_id = $status;
            $task->save();

            return response()->json([
                'message' => 'Task avviata',
                'task' => $task,
                'now' => now('Europe/Rome')
            ]);
        }

        $started_at = $task->started_at ?? null;

        //! PAUSA
        if ($status == 4) {
            $paused_at = now();
            $task->status_id = $status;
            $task->save();


            $time_interval = Carbon::parse($paused_at)->diffInMinutes($task->started_at);
            $parsed_minutes = intval($time_interval);

            $time_interval_data = [
                'task_id' => $task->id,
                'time' => $parsed_minutes
            ];

            $new_time_interval = Time_Interval::create($time_interval_data);

            return response()->json([
                'message' => 'Task in pausa!',
                'task' => $task,
                'time_interval_data' => $new_time_interval
            ]);
        }

        //! COMPLETATA
        if ($status == 3) {
            $completed_at = now();
            $task->status_id = $status;
            $task->save();
            $effective_time = 0;

            if ($task->started_at) {

                if (!$task->time_intervals()->exists()) {
                    $time_difference = Carbon::parse($completed_at)->diffInMinutes($task->started_at);
                    $effective_time = intval($time_difference);
                    $time_interval_data = [
                        'task_id' => $task->id,
                        'time' => $effective_time
                    ];

                    $new_time_interval = Time_Interval::create($time_interval_data);

                    $task->effective_time = $effective_time;
                    $task->save();

                    return response()->json([
                        'message' => 'success',
                        'task' => $task,
                        'completed_at' => $completed_at,
                        'effective_time' => $effective_time,
                        'if' => 'sono entrato nell-if'
                        // 'effective_time_message' => $effective_time_message,
                        // 'earned_time' => $earned_time,
                        // 'time_intervals' => $time_intervals
                    ]);

                }
                // else{

                //     $time_intervals = Time_Interval::where('task_id', $task->id)->pluck('time');

                //     $effective_time = array_sum($time_intervals);

                //     if ($task->estimated_time > $effective_time) {
                //         $earned_time = $task->estimated_time - $effective_time;
                //         $effective_time_message = 'Ci hai messo di meno di quanto pensavi!' . $earned_time;
                //     } else {
                //         $earned_time = $effective_time - $task->estimated_time;
                //         $effective_time_message = 'Ci hai messo di più di quanto pensavi!' . $earned_time;
                //     }
                // }

                return response()->json([
                    'message' => 'success',
                    'task' => $task,
                    'completed_at' => $completed_at,
                    'effective_time' => $effective_time,
                    'if' => 'non sono entrato nell-if'
                    // 'effective_time_message' => $effective_time_message,
                    // 'earned_time' => $earned_time,
                    // 'time_intervals' => $time_intervals
                ]);
            }
        }

        $task->status_id = $status;
        $task->save();

        return response()->json([
            'message' => 'status updated successfully',
            'task' => $task
        ]);
    }
}
