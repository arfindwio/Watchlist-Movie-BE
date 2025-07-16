<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('apiauth')->group(function () {
    // Auth
    Route::get('/authenticate',     [AuthController::class, 'authenticate']);
    Route::post('/profile', [AuthController::class, 'editProfile']);
    Route::delete('/profile/photo', [AuthController::class, 'deleteProfilePhoto']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Movie
    Route::get('/movies', [MovieController::class, 'getMovies']);
    Route::post('/movies/watchlist', [MovieController::class, 'createWatchlist']);
    Route::get('/movies/unwatched', [MovieController::class, 'getAllUnwatched']);
    Route::get('/movies/watched', [MovieController::class, 'getAllWatched']);
    Route::get('/movies/{id}', [MovieController::class, 'getDetailMovie']);
    Route::post('/movies/{id}', [MovieController::class, 'editMovieById']);
    Route::delete('/movies/{id}', [MovieController::class, 'deleteMovieById']);
});

