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

Route::group(['namespace' => 'Auth'], function() {
    Route::group(['prefix' => 'auth'], function() {
        Route::get('logout', 'LoginController@logout');
    });
});

Route::group(['prefix' => 'control', 'middleware' => 'core.menu'], function() {
    
	Route::group(['middleware' => 'core.auth'], function() {

		Route::group(['prefix' => 'post'], function() {
	        /*=============================================
	        =            Post CMS            =
	        =============================================*/
	        
	        	Route::get('master', 'CMS\Post@index')->middleware('can:menu-post')->name('cms.post-data.master');
			    Route::get('form', 'CMS\Post@create')->name('cms.post-data.create');
			    Route::post('form/{callback?}', 'CMS\Post@store')->middleware('can:create-post')->name('cms.post-data.store');
			    Route::put('form/{callback?}', 'CMS\Post@store')->name('cms.post-data.store');
			    Route::delete('form', 'CMS\Post@destroy')->name('cms.post-data.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'CMS\Post@serviceMaster')->middleware('can:menu-post')->name('cms.post-data.service-master');
				    Route::get('suggestion-related', 'CMS\Post@getSuggestionRelated')->middleware('can:menu-post')->name('cms.post-data.suggestion-related');
			    });
	        
	        /*=====  End of Post CMS  ======*/
		});
	});
});

Route::get('/_debugbar/assets/stylesheets', [
    'as' => 'debugbar-css',
    'uses' => '\Barryvdh\Debugbar\Controllers\AssetController@css'
]);

Route::get('/_debugbar/assets/javascript', [
    'as' => 'debugbar-js',
    'uses' => '\Barryvdh\Debugbar\Controllers\AssetController@js'
]);