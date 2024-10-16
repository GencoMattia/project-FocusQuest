<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Emotion;
use App\Models\MomentsType;
use Illuminate\Http\Request;

class ApiMomentController extends Controller
{
    public function getFormData(){
        $moment_types = MomentsType::all();
        $emotions = Emotion::all();

        return response()->json([
            'message'=>'success',
            'data'=>[
                'moment_types'=>$moment_types,
                'emotions'=>$emotions
            ]
        ]);
    }
}
