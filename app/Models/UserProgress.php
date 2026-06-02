<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Skill;

class UserProgress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'skill_id', 'status', 'started_at', 'completed_at'];

    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function skill() { return $this->belongsTo(Skill::class); }
}
