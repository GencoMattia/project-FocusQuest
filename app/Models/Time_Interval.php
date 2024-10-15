<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time_Interval extends Model
{
    use HasFactory;

    protected $table = 'time_intervals';

    protected $fillable = [
        'time', 'task_id'
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
