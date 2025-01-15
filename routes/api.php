<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('/me', [UserController::class, 'me'])
         ->middleware('auth:api')
         ->name('me');

    Route::apiResource('animal', AnimalController::class)
         ->middleware('auth:api');

    Route::apiResource('shelter', ShelterController::class)
         ->middleware('auth:api');

    Route::apiResource('species', SpeciesController::class)
         ->middleware('auth:api');
});
