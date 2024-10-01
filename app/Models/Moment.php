<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moment extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'message', 'started_at', 'ended_at', 'moment_img'
    ];

    public function momentstype(){
        return $this->belongsTo(MomentsType::class);
    }

    public function emotion(){
        return $this->belongsTo(Emotion::class);
    }

    public function tasks(){
        return $this->belongsToMany(Task::class);
    }
}
