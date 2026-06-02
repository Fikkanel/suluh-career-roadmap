<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CareerController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\RoadmapController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\PivotController;
use App\Http\Controllers\Api\SkillGapController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ImpactController;

/*
|--------------------------------------------------------------------------
| Public Routes (no auth)
|--------------------------------------------------------------------------
*/
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login'])->middleware('basic.auth');

/*
|--------------------------------------------------------------------------
| JWT-Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('user/profile', [ProfileController::class, 'show']);
    Route::put('user/profile', [ProfileController::class, 'update']);

    Route::post('assessment/submit', [AssessmentController::class, 'submit']);
    Route::get('roadmap/current', [RoadmapController::class, 'current']);
    Route::patch('progress/update', [ProgressController::class, 'update']);
    Route::get('progress/summary', [ProgressController::class, 'summary']);
    Route::post('roadmap/pivot', [PivotController::class, 'store']);
    Route::get('skill-gap', [SkillGapController::class, 'show']);
    Route::get('export/json', [ExportController::class, 'json']);
    Route::get('export/pdf', [ExportController::class, 'pdf']);
});

/*
|--------------------------------------------------------------------------
| API Key-Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('api.key')->group(function () {
    Route::get('careers', [CareerController::class, 'index']);
    Route::get('impact/public', [ImpactController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Research API — Fase 3 (Publik, Agregat Anonim)
| Auth: Header X-Research-Key (sama dengan API_KEY di .env)
|--------------------------------------------------------------------------
*/
Route::prefix('research')
    ->middleware('api.key')
    ->name('research.')
    ->group(function () {
        Route::get('summary',              [\App\Http\Controllers\Api\ResearchController::class, 'summary'])->name('summary');
        Route::get('career-distribution',  [\App\Http\Controllers\Api\ResearchController::class, 'careerDistribution'])->name('career-distribution');
        Route::get('crs-trend',            [\App\Http\Controllers\Api\ResearchController::class, 'crsTrend'])->name('crs-trend');
        Route::get('pivot-analysis',       [\App\Http\Controllers\Api\ResearchController::class, 'pivotAnalysis'])->name('pivot-analysis');
    });
