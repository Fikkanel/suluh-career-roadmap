<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'google_id', 'personality_scores',
        'public_username', 'is_profile_public',
        'age_range', 'education_level', 'work_experience', 'province',
        'exploration_readiness', 'support_level', 'current_career_id', 'is_admin', 'role',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'personality_scores'  => 'encrypted:array',
            'is_admin'            => 'boolean',
            'is_profile_public'   => 'boolean',
        ];
    }

    public function assessmentResults() { return $this->hasMany(AssessmentResult::class); }
    public function progress()          { return $this->hasMany(UserProgress::class); }
    public function roadmapArchives()   { return $this->hasMany(RoadmapArchive::class); }
    public function contextScore()      { return $this->hasOne(ContextScore::class); }
    public function currentCareer()     { return $this->belongsTo(Career::class, 'current_career_id'); }
    public function skillValidations()  { return $this->hasMany(SkillValidation::class); }

    public function isAdmin(): bool     { return (bool) $this->is_admin; }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
