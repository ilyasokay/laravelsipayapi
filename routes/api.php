<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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
Route::any('/postdata', [HomeController::class, 'postData'])->name('postdata');

// Hash Generate
Route::resource('post', 'App\Http\Controllers\PostController');

Route::any('success', 'App\Http\Controllers\HomeController@apiSuccess');
Route::any('fail', 'App\Http\Controllers\HomeController@apifail');

Route::post('hash/{password}', 'App\Http\Controllers\HomeController@apiHash');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
