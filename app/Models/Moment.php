<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moment extends Model
{
    use HasFactory;
    protected $fillable = [
        'moments_type_id',
        'emotion_id',
        'name', 'message', 'moment_img',
        'task_id',
    ];

    public function momentsType(){
        return $this->belongsTo(MomentsType::class, 'moments_type_id');
    }

    public function emotion(){
        return $this->belongsTo(Emotion::class);
    }

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
