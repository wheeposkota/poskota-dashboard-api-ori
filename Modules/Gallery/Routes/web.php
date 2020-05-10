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

		Route::group(['prefix' => 'gallery'], function() {

			Route::group(['prefix' => 'photo'], function() {
		        /*=============================================
		        =            Photo CMS            =
		        =============================================*/
		        
				    Route::get('/', 'PhotoController@index')->middleware('can:menu-gallery')->name('gallery.photo');
				    Route::get('form', 'PhotoController@create')->name('gallery.photo');
				    Route::post('form', 'PhotoController@store')->middleware('can:create-gallery');
				    Route::put('form', 'PhotoController@store');
				    Route::delete('form', 'PhotoController@destroy');

				    Route::group(['prefix' => 'api'], function() {
					    Route::get('master', 'PhotoController@serviceMaster')->middleware('can:menu-gallery');
				    });
		        
		        /*=====  End of Photo CMS  ======*/
			});

			Route::group(['prefix' => 'video'], function() {
		        /*=============================================
		        =            Photo CMS            =
		        =============================================*/
		        
				    Route::get('/', 'VideoController@index')->middleware('can:menu-gallery')->name('gallery.video');
				    Route::get('form', 'VideoController@create')->name('gallery.video');
				    Route::post('form', 'VideoController@store')->middleware('can:create-gallery');
				    Route::put('form', 'VideoController@store');
				    Route::delete('form', 'VideoController@destroy');

				    Route::group(['prefix' => 'api'], function() {
					    Route::get('master', 'VideoController@serviceMaster')->middleware('can:menu-gallery');
				    });
		        
		        /*=====  End of Photo CMS  ======*/
			});

		});

        
	});
});
