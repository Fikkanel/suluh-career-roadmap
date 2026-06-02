<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImpactSurvey extends Model
{
    protected $fillable = [
        'user_id',
        'pseudonym_id',
        'type',
        'crs_before',
        'crs_after',
        'answers',
        'submitted_at',
    ];

    protected $casts = [
        'answers'      => 'encrypted:array',
        'submitted_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->pseudonym_id)) {
                $model->pseudonym_id = bin2hex(random_bytes(16));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
