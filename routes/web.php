<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\WebHookController;

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
  Route::get('/log-create/{data}', [HomeController::class, 'createLog'])->name('log.create');
  Route::get('/basket', [BasketController::class, 'index'])->name('basket.index');
  Route::post('/basket/{product_id}/add', [BasketController::class, 'basketAdd'])->name('basket.add');
  Route::get('/basket/{id}/remove', [BasketController::class, 'basketItemRemove'])->name('basket.item.remove');
  Route::get('/basket-count', [BasketController::class, 'basketCount'])->name('basket.count');

  Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
  Route::post('/payment', [PaymentController::class, 'store'])->name('payment.store');

  Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
  Route::get('/order/{invoice_id}/checkstatus', [OrderController::class, 'checkStatus'])->name('order.checkstatus');
  Route::get('/order/{invoice_id}/status', [OrderController::class, 'getStatus'])->name('order.status');
  Route::get('/order/{order_id}/refund', [OrderController::class, 'refund'])->name('order.refund');

  Route::post('/pos', [HomeController::class, 'getPos'])->name('pos');
  Route::post('/installments', [HomeController::class, 'installments'])->name('installments');
  Route::get('/commissions', [HomeController::class, 'commissions'])->name('commissions');

  //Route::get('/cards', [CardController::class, 'getSaveCards'])->name('cards');
  Route::get('/cards', [CardController::class, 'getCardTokens'])->name('cards');
  Route::get('/savecard', [CardController::class, 'saveCard'])->name('savecard');
  Route::get('/editcard/{card_token}', [CardController::class, 'editCard'])->name('editcard');
  Route::post('/editcard/{card_token}', [CardController::class, 'editCard'])->name('editcard');
  Route::post('/deletecard/{card_token}', [CardController::class, 'deleteCard'])->name('deletecard');

  Route::get('/paySmart3D', [HomeController::class, 'paySmart3D'])->name('paySmart3D');
  Route::get('/paySmart2D', [HomeController::class, 'paySmart2D'])->name('paySmart2D');

  Route::get('/success', [HomeController::class, 'success'])
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
      ->name('success');
  Route::get('/fail', [HomeController::class, 'fail'])
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
      ->name('fail');
  Route::get('/hash', [HomeController::class, 'hash'])->name('hash');
  Route::post('/hash', [HomeController::class, 'hash'])->name('hash');
  Route::get('/token', [HomeController::class, 'token'])->name('token');
  Route::get('/transaction', [HomeController::class, 'getTransactions'])->name('transaction');
  Route::post('/transaction', [HomeController::class, 'getTransactions'])->name('transaction');




  Route::any('/sale-web-hook', [WebHookController::class, 'saleWebHook'])
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
      ->name('webhook.sale');

  Route::any('/recurring-web-hook', [WebHookController::class, 'recurringWebHook'])
      ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
      ->name('webhook.recurring');





Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
