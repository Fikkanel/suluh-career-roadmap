<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Career;
use App\Models\UserProgress;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = ['career_id', 'name', 'level', 'estimated_hours', 'resources', 'order', 'validation_type', 'scenario_question'];

    protected $casts = ['resources' => 'array'];

    public function career() { return $this->belongsTo(Career::class); }
    public function userProgress() { return $this->hasMany(UserProgress::class); }
    public function validations() { return $this->hasMany(SkillValidation::class); }
}
