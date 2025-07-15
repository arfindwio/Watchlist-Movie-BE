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
    Route::post('/logout', [AuthController::class, 'logout']);

    // Movie
    Route::get('/watchlists', [MovieController::class, 'getWatchlists']);
    Route::post('/watchlists', [MovieController::class, 'createWatchlist']);
    Route::get('/watchlists/{id}', [MovieController::class, 'getDetailWatchlist']);
    Route::post('/watchlists/{id}', [MovieController::class, 'editWatchlistById']);
    Route::delete('/watchlists/{id}', [MovieController::class, 'deleteWatchlistById']);
});

