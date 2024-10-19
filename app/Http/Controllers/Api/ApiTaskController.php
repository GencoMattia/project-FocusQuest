<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewTaskRequest;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Task;
use App\Models\Pause;
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
            ->with(['priority', 'status', 'category', 'moments'])
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
            // ->where('status_id', $status_id)
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No tasks found for this user.'], 404);
        }

        return response()->json($tasks);
    }

    public function getTopPriorityTask(Request $request)
    {
        $estimatedTimeOrder = $request->input("estimated_time_order", "asc");

        $task = Task::where("user_id", auth()->id())
            ->whereNotNull("priority_id")
            ->orderBy("priority_id", "asc")
            ->orderBy("deadline", "asc")
            ->orderBy("estimated_time", $estimatedTimeOrder)
            ->first();

        if ($task) {
            return response()->json(["message" => "Top priority task retrieved successfully", "task" => $task]);
        }

        return response()->json(["message" => "No Tasks found"], 404);
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

        $task->status_id = $status;
        $task->save();

        //! AVVIO
        if ($status == 2) {

            //? se esistono pause
            if ($task->number_of_pauses>=1) {
                $task->resumed_at = now();
                $total_rest_time = $task->resumed_at->diffInMinutes($task->paused_at);
                $task->rest_time += $total_rest_time;
                $rest_time_message = 'Tutto funziona';

                $task->status_id = $status;
                $task->save();

                return response()->json([
                    'message' => 'Task riavviata',
                    'task' => $task,
                    'task_rest_time'=> $task->rest_time,
                    'message'=> $rest_time_message
                ]);
            }
            else {
                $task->started_at = now();
                $task->status_id = $status;
                $task->save();

                return response()->json([
                    'message' => 'Task avviata',
                    'task' => $task,
                ]);
            }
        }


        //! PAUSA
        if ($status == 4) {
            $task->paused_at = now();
            $task->status_id = $status;
            $task->number_of_pauses++;
            $task->save();

            return response()->json([
                'message' => 'Task in pausa!',
                'task' => $task,
                'number_of_pauses' => $task->number_of_pauses,
            ]);
        }

        //! COMPLETATA
        if ($status == 3) {
            $task->ended_at = now();
            $task->status_id = $status;
            $task->save();
            $effective_time = 0;

            if ($task->started_at) {

                //**! SE ESISTONO PAUSE */
                if ($task->number_of_pauses>=1) {

                    $total_time_with_pauses = Carbon::parse($task->ended_at)->diffInMinutes($task->started_at);

                    if ($task->estimated_time > $effective_time) {
                        $earned_time = $task->estimated_time - $effective_time;
                        $effective_time_message = 'Ci hai messo di meno di quanto pensavi! ' . 'Hai guadagnato ' . $earned_time . ' minuti';
                    } else {
                        $earned_time = $effective_time - $task->estimated_time;
                        $effective_time_message = 'Ci hai messo di più di quanto pensavi! ' . 'Ci hai messo ' . $earned_time . ' minuti in più';
                    }

                    $task->status_id = $status;
                    $task->effective_time = $effective_time;
                    $task->save();

                    return response()->json([
                        'message' => 'Task completata con pause',
                        'total_task_time' => $this->formatTime($total_time_with_pauses),
                        'effective_task_time' => $this->formatTime($effective_time),
                        // 'total_pause_time' => $this->formatTime($total_pause_time),
                        'effective_time_message' => $effective_time_message,
                        'task' => $task
                    ]);
                }
                //**! SE NON ESISTONO PAUSE */
                else {
                    //**Calcolo la durata in minuti della task e la parso come un intero */
                    $unparsed_effective_time = Carbon::parse($task->started_at)->diffInMinutes(Carbon::parse($task->ended_at));
                    $effective_time = intval($unparsed_effective_time);

                    //**Diversi messaggi in base alla differenza fra tempo stimato e tempo effettivo */
                    if ($task->estimated_time > $effective_time) {
                        $earned_time = $task->estimated_time - $effective_time;
                        $effective_time_message = 'Ci hai messo di meno di quanto pensavi! ' . 'Hai guadagnato ' . $earned_time . ' minuti';
                    } else {
                        $earned_time = $effective_time - $task->estimated_time;
                        $effective_time_message = 'Ci hai messo di più di quanto pensavi! ' . 'Ci hai messo ' . $earned_time . ' minuti in più';
                    }

                    //**Aggiorno lo stato della task */
                    $task->status_id = $status;
                    $task->effective_time = $effective_time;
                    $task->save();

                    return response()->json([
                        'message' => 'Task completata senza pause',
                        'effective_task_time' => $effective_time,
                        'effective_time_message' => $effective_time_message,
                        'task' => $task
                    ]);
                }
            }
        }

        $task->status_id = $status;
        $task->save();

        return response()->json([
            'message' => 'status updated successfully',
            'task' => $task
        ]);
    }

    //*! UTILITIES *//
    private function formatTime($minutes) {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        return "{$hours} ore {$remainingMinutes} minuti";
    }

    public function suggestTasks(Request $request)
    {
        $query = $request->input("query");
        $tasks = Task::where('name', 'like', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('name');

        return response()->json(['tasks' => $tasks]);
    }
}
