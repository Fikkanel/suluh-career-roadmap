<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoadmapArchive extends Model
{
    protected $fillable = ['user_id', 'career_id', 'career_name', 'reflection', 'completed_skills', 'total_skills', 'snapshot', 'archived_at'];

    protected $casts = ['snapshot' => 'array', 'archived_at' => 'datetime'];

    public function user()   { return $this->belongsTo(User::class); }
    public function career() { return $this->belongsTo(Career::class); }
}
