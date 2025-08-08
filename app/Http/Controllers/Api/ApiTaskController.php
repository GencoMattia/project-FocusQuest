<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewTaskRequest;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiTaskController extends Controller
{
    private const STATUS_NEW = 1;
    private const STATUS_IN_PROGRESS = 2;
    private const STATUS_COMPLETED = 3;
    private const STATUS_PAUSED = 4;

    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $task->load(['priority', 'status', 'category', 'moments']);

        return response()->json([
            'message' => 'success',
            'task' => $task
        ]);
    }

    public function store(CreateNewTaskRequest $request)
    {
        $data = $request->validated();

        // $deadline = $data["deadline"] ?? now()->toDateString();

        $newTask = Task::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'estimated_time' => $data['estimated_time'],
            'user_id' => auth()->id(),
            'category_id' => $data['category_id'],
            'priority_id' => $data['priority_id'],
            'status_id' => self::STATUS_NEW,
            'deadline' => $data["deadline"] ?? now()->toDateString(),
        ]);

        return response()->json(['message' => 'Task created successfully', 'task' => $newTask]);
    }

    public function getUserTask()
    {
        $authenticated_user_id = auth()->user()->id;
        $tasks = Task::with(['priority','status','category'])
            ->where('user_id', $authenticated_user_id)
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


    public function modifyTaskStatus(Request $request, Task $task)
    {
        $data = $request->validate([
            'status_id' => 'required|integer|exists:statuses,id',
        ]);

        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $status = (int) $data['status_id'];

        //! AVVIO
        if ($status === self::STATUS_IN_PROGRESS) {

            //? se esistono pause
            if ((int)($task->number_of_pauses ?? 0) >= 1) {
                $task->resumed_at = now();
                // paused_at may be null for data inconsistencies
                if ($task->paused_at) {
                    $total_rest_time = $task->resumed_at->diffInMinutes($task->paused_at);
                    $task->rest_time = (int)($task->rest_time ?? 0) + $total_rest_time;
                }

                $task->status_id = $status;
                $task->save();

                return response()->json([
                    'message' => 'Task riavviata',
                    'task' => $task,
                    'task_rest_time'=> $task->rest_time,
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
        if ($status === self::STATUS_PAUSED) {
            $task->paused_at = now();
            $task->status_id = $status;
            $task->number_of_pauses = (int)($task->number_of_pauses ?? 0) + 1;
            $task->save();

            return response()->json([
                'message' => 'Task in pausa!',
                'task' => $task,
                'number_of_pauses' => $task->number_of_pauses,
            ]);
        }

        //! COMPLETATA
        if ($status === self::STATUS_COMPLETED) {
            $task->ended_at = now();
            $task->status_id = $status;
            $task->save();
            $effective_time = 0;

            if ($task->started_at) {

                //**! SE ESISTONO PAUSE */
                if ((int)($task->number_of_pauses ?? 0) >= 1) {

                    $total_time_with_pauses = Carbon::parse($task->ended_at)->diffInMinutes($task->started_at);
                    $pause_minutes = (int)($task->rest_time ?? 0);
                    $effective_time = max(0, $total_time_with_pauses - $pause_minutes);

                    if ($task->estimated_time > $effective_time) {
                        $earned_time = $task->estimated_time - $effective_time;
                        $effective_time_message = 'Ci hai messo di meno di quanto pensavi! ' . 'Hai guadagnato ' . $earned_time . ' minuti';
                    } else {
                        $earned_time = $effective_time - $task->estimated_time;
                        $effective_time_message = 'Ci hai messo di più di quanto pensavi! ' . 'Ci hai messo ' . $earned_time . ' minuti in più';
                    }

                    $task->effective_time = $effective_time;
                    $task->save();

                    return response()->json([
                        'message' => 'Task completata con pause',
                        'total_task_time' => $this->formatTime($total_time_with_pauses),
                        'total_pause_time' => $this->formatTime($pause_minutes),
                        'effective_task_time' => $this->formatTime($effective_time),
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
