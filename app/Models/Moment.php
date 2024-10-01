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
        return $this->hasOne(MomentsType::class);
    }
}
