<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateNewMomentRequest;
use App\Models\Emotion;
use App\Models\Task;
use App\Models\MomentsType;
use App\Models\Moment;
use Illuminate\Http\Request;

class ApiMomentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function getFormData(Request $request){
        $validated = $request->validate([
            'task_id'=> 'nullable|integer|exists:tasks,id'
        ]);

        $task = null;
        if (!empty($validated['task_id'])) {
            $task = Task::find($validated['task_id']);
            if ($task && $task->user_id !== auth()->id()) {
                return response()->json(['message' => 'Not Found'], 404);
            }
        }
        $moment_types = MomentsType::all();
        $emotions = Emotion::all();

        return response()->json([
            'message'=>'success',
            'data'=>[
                'moment_types'=>$moment_types,
                'emotions'=>$emotions,
                'task'=>$task
            ]
        ]);
    }

    public function store(CreateNewMomentRequest $request, Task $task){
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validated();
        $payload = array_merge($data, ['task_id' => $task->id]);
        Moment::create($payload);

        return response()->json([
            'message'=> 'Momento creato con successo'
        ]);
    }
}
