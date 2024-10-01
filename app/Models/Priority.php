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

    public function categories(){
        return $this->belongsToMany(Task::class);
    }
}
