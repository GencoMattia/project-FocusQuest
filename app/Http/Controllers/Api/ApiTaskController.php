<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class ApiTaskController extends Controller
{
    public function create(CreateNewTaskRequest $request){
        $data = $request->validated();
        $user = auth()->user();
        $newTask = new Task();

        $newTask["name"] = $data["name"];
        $newTask["description"] = $data["description"] ?? null;
        $newTask["deadline"] = $data["deadline"];
        $newTask["estimated_time"] = $data["estimated_time"];
        $newTask["effective_time"] = $data["effective_time"];

        $newTask["user_id"] = $user->id;
        $newTask["category_id"] = $data["category_id"];
        $newTask["priority_id"] = $data["priority_id"];
        $newTask["status_id"] = 1;

        $newTask->save();


    }
}
