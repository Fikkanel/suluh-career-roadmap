<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContextScore extends Model
{
    protected $fillable = [
        'user_id',
        'pseudonym_id',
        'score',
        'level',
        'factors',
    ];

    protected $casts = [
        'factors' => 'encrypted:array',
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
