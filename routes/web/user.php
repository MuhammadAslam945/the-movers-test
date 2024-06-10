<?php

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
|
| These Routes are common routes
|
 */

/*
 * Temporary dummy route for testing SPA.
 */
use App\Http\Controllers\jazzcashUserController;
Route::prefix('web-user')->namespace ('User')->group(function () {

    Route::get('login', 'WebUserControllerController@viewLogin');


});

//Route::prefix('user')->namespace('User')->middleware('auth')->group(function () {

    Route::get('/add-jazz', [jazzcashUserController::class, 'index'])->name('jazzcash');
    Route::post('/add-funds', [jazzcashUserController::class, 'addTransaction'])->name('addFunds');
    Route::get('/error', [jazzcashUserController::class, 'error'])->name('jazzerror');//
//});
