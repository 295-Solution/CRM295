<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;

Route::name('api.')->middleware('auth:sanctum')->group(function () {
	Route::middleware('throttle:api-read')->group(function () {
		Route::apiResource('clients', ClientController::class)
			->only(['index', 'show']);
	});

	Route::apiResource('clients', ClientController::class)
		->only(['store', 'update', 'destroy'])
		->middleware('throttle:api-write');
});