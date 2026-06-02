<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['prompt', 'context', 'riasec_category', 'big_five_trait', 'weight', 'type', 'options', 'is_active', 'order'];

    protected $casts = ['options' => 'array', 'is_active' => 'boolean', 'weight' => 'float'];
}
