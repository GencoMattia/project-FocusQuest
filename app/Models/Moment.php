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

    public function momentstype(){
        return $this->belongsTo(MomentsType::class);
    }

    public function emotion(){
        return $this->belongsTo(Emotion::class);
    }

    public function tasks(){
        return $this->belongsTo(Task::class);
    }
}
