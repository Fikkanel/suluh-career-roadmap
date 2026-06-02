<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\SkillProgressController;
use App\Http\Controllers\PivotController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\SkillValidationController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminManagementController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'landing'])->name('landing');
Route::get('/impact', [PublicController::class, 'impact'])->name('impact');
Route::get('/sunset-policy', [PublicController::class, 'sunsetPolicy'])->name('sunset-policy');
Route::get('/api-docs', fn() => view('public.api-docs'))->name('api-docs');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Institution Self-Register
    Route::get('/register/institution', [AuthController::class, 'showRegisterInstitution'])->name('register.institution');
    Route::post('/register/institution', [AuthController::class, 'registerInstitution'])->name('register.institution.post');

    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated App Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/assessment', [AssessmentController::class, 'show'])->name('assessment');
    Route::post('/assessment', [AssessmentController::class, 'submit'])->name('assessment.submit');
    Route::post('/assessment/save-draft', [AssessmentController::class, 'saveDraft'])->name('assessment.saveDraft');
    Route::get('/assessment/result', [AssessmentController::class, 'result'])->name('assessment.result');

    Route::get('/career/{id}', [CareerController::class, 'show'])->name('career.detail');
    Route::post('/career/{id}/choose', [CareerController::class, 'choose'])->name('career.choose');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/roadmap', [RoadmapController::class, 'index'])->name('roadmap');

    Route::get('/skill-progress', [SkillProgressController::class, 'index'])->name('skill-progress');
    Route::patch('/skill-progress/{skillId}', [SkillProgressController::class, 'update'])->name('skill-progress.update');

    // Skill Validation (FR-09)
    Route::get('/skill/{skillId}/validate', [SkillValidationController::class, 'show'])->name('skill-validation.show');
    Route::post('/skill/{skillId}/validate', [SkillValidationController::class, 'store'])->name('skill-validation.store');

    // Impact Surveys (FR-14)
    Route::get('/survey/{type}', [SurveyController::class, 'show'])->name('survey.show');
    Route::post('/survey/{type}', [SurveyController::class, 'store'])->name('survey.store');

    Route::get('/pivot', [PivotController::class, 'show'])->name('pivot');
    Route::post('/pivot', [PivotController::class, 'store'])->name('pivot.store');

    Route::get('/archive', [ArchiveController::class, 'index'])->name('archive');

    Route::get('/export', [ExportController::class, 'index'])->name('export');
    Route::post('/export/pdf', [ExportController::class, 'pdf'])->name('export.pdf');
    Route::post('/export/json', [ExportController::class, 'json'])->name('export.json');
    
    // Notifications (FR-XX / Phase 2)
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Public Profile Settings
    Route::get('/profile/settings', [\App\Http\Controllers\PublicProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/settings', [\App\Http\Controllers\PublicProfileController::class, 'updateSettings'])->name('profile.settings.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\PublicProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
});

// Public Profile Route (Bisa diakses tanpa login)
Route::get('/u/{username}', [\App\Http\Controllers\PublicProfileController::class, 'show'])->name('public.profile');

// Komite Etika Data (FR-XX / Phase 2 Item 5)
Route::get('/ethics', [\App\Http\Controllers\DataEthicsController::class, 'index'])->name('ethics');
Route::post('/ethics/{id}/vote', [\App\Http\Controllers\DataEthicsController::class, 'vote'])->name('ethics.vote')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/management', [AdminManagementController::class, 'index'])->name('management');

    // Career CRUD
    Route::post('/careers', [AdminManagementController::class, 'storeCareer'])->name('careers.store');
    Route::put('/careers/{career}', [AdminManagementController::class, 'updateCareer'])->name('careers.update');
    Route::delete('/careers/{career}', [AdminManagementController::class, 'destroyCareer'])->name('careers.destroy');

    // Question CRUD
    Route::post('/questions', [AdminManagementController::class, 'storeQuestion'])->name('questions.store');
    Route::put('/questions/{question}', [AdminManagementController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/questions/{question}', [AdminManagementController::class, 'destroyQuestion'])->name('questions.destroy');

    // Ethics Decision CRUD
    Route::post('/ethics', [AdminManagementController::class, 'storeEthics'])->name('ethics.store');
    Route::delete('/ethics/{ethics}', [AdminManagementController::class, 'destroyEthics'])->name('ethics.destroy');
});

/*
|--------------------------------------------------------------------------
| Mentor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'mentor'])->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Mentor\MentorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/mentee/{userId}', [\App\Http\Controllers\Mentor\MentorDashboardController::class, 'showMentee'])->name('mentee.show');
    Route::post('/mentee/{userId}/feedback', [\App\Http\Controllers\Mentor\MentorDashboardController::class, 'storeFeedback'])->name('feedback.store');
});

/*
|--------------------------------------------------------------------------
| Institution Routes (Fase 3)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'institution'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Institution\InstitutionDashboardController::class, 'index'])->name('dashboard');
});

