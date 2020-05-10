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

Route::group(['prefix' => 'control', 'middleware' => 'core.menu'], function() {

    Route::group(['middleware' => 'core.auth'], function() {

        /*=============================================
        =            Setting CMS            =
        =============================================*/
        
		    Route::get('broadcast', 'BroadcastController@create')->middleware('can:menu-broadcast')->name('cms.broadcast.create');
		    Route::put('broadcast', 'BroadcastController@store')->middleware('can:update-broadcast')->name('cms.broadcast.store');
        
        /*=====  End of Setting CMS  ======*/

        
	});
});
