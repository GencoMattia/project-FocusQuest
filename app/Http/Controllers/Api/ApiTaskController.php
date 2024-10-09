<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewTaskRequest;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class ApiTaskController extends Controller
{
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
        $tasks = Task::where('user_id', $authenticated_user_id)->get();

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
}
