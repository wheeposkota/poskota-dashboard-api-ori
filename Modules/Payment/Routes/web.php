<?php

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

Route::post('webhook/payment', 'PaymentController@webhook');

Route::group(['prefix' => 'control', 'middleware' => 'core.menu'], function() {

	Route::group(['prefix' => 'payment'], function() {
	    Route::get('e-payment', 'PaymentController@ePayment');
	    Route::get('incash', 'PaymentController@inCash');
	    Route::get('callback', 'PaymentController@callback');
	});
	
});
