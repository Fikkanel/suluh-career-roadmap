<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EthicsDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'context', 'decision', 'status', 'votes_for', 'votes_against', 'implementation_date'
    ];

    protected $casts = [
        'implementation_date' => 'date'
    ];
}
