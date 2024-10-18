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
    public function getMomentData(Request $request){
        $emotion_id = $request->emotion_id;
        $moments_type_id = $request->moments_type_id;
        $task_id= $request->task_id;

        $moment_emotion = Emotion::findOrFail($emotion_id);
        $moment_moments_type = MomentsType::findOrFail($moments_type_id);
        $moment_task= Task::findOrFail($task_id);

        return response()->json([
            'message' => 'dati del componente Moment recuperati correttamente',
            'moment_emotion' => $moment_emotion,
            'moment_moments_type'=> $moment_moments_type,
            'moment_task' =>$moment_task
        ]);
    }

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

        return response()->json([
            'message'=> 'Momento creato con successo'
        ]);
    }
}
