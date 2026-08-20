<?php

use App\Http\Controllers\Api\IngestionController;
use Illuminate\Support\Facades\Route;

// See App\Http\Controllers\Api\IngestionController's docblock for the
// contract this defines (plan §4 "Ingestion API contract").
Route::post('/ingestion/profiles', [IngestionController::class, 'store'])
    ->middleware('ingestion.key')
    ->name('api.ingestion.profiles.store');

// Freshness-check cron endpoint lands in Phase 6 — see plan §7.
