<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Skill;
use App\Models\AssessmentResult;

class Career extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'riasec_code', 'industry_standard', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function skills()
    {
        return $this->hasMany(Skill::class)->orderBy('order');
    }

    public function assessmentResults()
    {
        return $this->hasMany(AssessmentResult::class, 'chosen_career_id');
    }
}
