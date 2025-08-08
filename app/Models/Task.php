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
        'name',
        'description',
        'deadline',
        'estimated_time',
        'effective_time'
    ];

    protected $casts = [
        'deadline' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'paused_at' => 'datetime',
        'resumed_at' => 'datetime',
        'estimated_time' => 'integer',
        'effective_time' => 'integer',
        'rest_time' => 'integer',
        'number_of_pauses' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function moments()
    {
        return $this->hasMany(Moment::class);
    }

    /**
     * If the deadline isn't filled by the user, it will be set the value to the odiern date.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($task) {
            if (empty($task->deadline)) {
                $task->deadline = now()->toDateString();
            }
            // default counters to 0
            $task->number_of_pauses = $task->number_of_pauses ?? 0;
            $task->rest_time = $task->rest_time ?? 0;
        });
    }
}
