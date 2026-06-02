<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorFeedback extends Model
{
    protected $fillable = [
        'mentor_id',
        'user_id',
        'roadmap_archive_id',
        'content',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roadmapArchive()
    {
        return $this->belongsTo(RoadmapArchive::class);
    }
}
