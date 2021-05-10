<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

  Route::get('/', [HomeController::class, 'index'])->name('index');
  Route::post('/installment', [HomeController::class, 'installment'])->name('installment');
  Route::get('/commissions', [HomeController::class, 'commissions'])->name('commissions');
  Route::post('/payment', [HomeController::class, 'payment'])->name('payment');

  //Route::get('/paySmart3D', [HomeController::class, 'paySmart3D'])->name('paySmart3D');
  //Route::get('/paySmart2D', [HomeController::class, 'paySmart2D'])->name('paySmart2D');

  Route::get('/success', [HomeController::class, 'success'])->name('success');
  Route::get('/fail', [HomeController::class, 'fail'])->name('fail');
  Route::get('/hash/{invoice_id}', [HomeController::class, 'hash'])->name('hash');
  Route::get('/token', [HomeController::class, 'token'])->name('token');


