<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'priority_id',
        'status_id',
        'name', 'description',
        // 'deadline',
        'estimated_time', 'effective_time'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function priority(){
        return $this->belongsTo(Priority::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function moments(){
        return $this->hasMany(Moment::class);
    }

    public function time_intervals(){
        return $this->hasMany(Time_Interval::class);
    }

    public function pauses(){
        return $this->hasMany(Pause::class);
    }
}
