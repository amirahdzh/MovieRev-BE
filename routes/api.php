<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CastController;
use App\Http\Controllers\API\CastMovieController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\MovieController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::apiResource('cast', CastController::class);
    Route::apiResource('genre', GenreController::class);
    Route::get('/genre/{genreId}/movies', [GenreController::class, 'getMoviesByGenre']);
    Route::apiResource('movie', MovieController::class);
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('/generate-otp-code', [AuthController::class, 'generateOtpCode'])->middleware('auth:api');
        Route::post('/account-verification', [AuthController::class, 'verification'])->middleware('auth:api');
    });
    Route::get('/me', [AuthController::class, 'getUser'])->middleware('auth:api');
    Route::post('/update-user', [AuthController::class, 'updateUser'])->middleware('auth:api', 'isVerificationAccount');
    Route::apiResource('role', RoleController::class)->middleware('auth:api', 'isAdmin');
    Route::post('/profile', [ProfileController::class, 'store'])->middleware('auth:api', 'isVerificationAccount');
    Route::post('/review', [ReviewController::class, 'store'])->middleware('auth:api', 'isVerificationAccount');
    Route::apiResource('cast-movie', CastMovieController::class);
    Route::get('cast-movie/movie/{movieId}', [CastMovieController::class, 'getCastsForMovie']);
    Route::get('/cast-movie/get-id', [CastMovieController::class, 'getCastMovieId']);
});
