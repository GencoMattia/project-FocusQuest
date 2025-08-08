<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $fillable =[
        'name', 'color', 'level', 'description'
    ];

    /**
     * @deprecated Use tasks() instead.
     */
    public function categories(){
        return $this->hasMany(Task::class);
    }

    public function tasks(){
        return $this->hasMany(Task::class);
    }
}
