<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'control'], function() {
	Route::group(['prefix' => 'ads/iklan-umum-khusus/transaction'], function() {
		Route::post('save', 'TrxAdsClassicController@store');
	});
});
