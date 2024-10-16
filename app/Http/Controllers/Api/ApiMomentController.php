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
    public function getFormData(Request $request){
        $task_id = $request->validate([
            'task_id'=> 'required|integer|exists:tasks,id'
        ]);

        $task = Task::findOrFail($task_id);
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

    public function store(CreateNewMomentRequest $request){
        $data = $request->validated();

        Moment::create($data);
    }
}
