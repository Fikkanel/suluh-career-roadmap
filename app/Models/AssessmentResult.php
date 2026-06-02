<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Career;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'pseudonym_id', 'riasec_scores', 'big_five_scores', 'top_career_ids', 'crs', 'chosen_career_id'];

    protected $casts = ['riasec_scores' => 'encrypted:array', 'big_five_scores' => 'encrypted:array', 'top_career_ids' => 'array'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->pseudonym_id)) {
                $model->pseudonym_id = bin2hex(random_bytes(16));
            }
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function chosenCareer() { return $this->belongsTo(Career::class, 'chosen_career_id'); }
}
