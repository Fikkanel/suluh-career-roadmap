<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LlmNarrativeCache extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'profile_hash',
        'narrative',
        'provider',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
