<?php

use App\Http\Controllers\FacebookAuthenticationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/logout', [FacebookAuthenticationController::class, FacebookAuthenticationController::ACTION_LOGOUT]);
Route::get('/login', [FacebookAuthenticationController::class, FacebookAuthenticationController::ACTION_LOGIN]);
Route::get('/', [FacebookAuthenticationController::class, FacebookAuthenticationController::ACTION_INDEX]);
