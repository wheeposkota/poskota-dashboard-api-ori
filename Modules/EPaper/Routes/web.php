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

		Route::group(['prefix' => 'e-paper'], function() {

	        /*=============================================
	        =            EPaper CMS            =
	        =============================================*/
	        
			    Route::get('/', 'EPaperController@index')->middleware('can:menu-epaper')->name('epaper.resource.master');
			    Route::get('form', 'EPaperController@create')->name('epaper.resource.create');
			    Route::post('form', 'EPaperController@store')->middleware('can:create-epaper')->name('epaper.resource.store');
			    Route::put('form', 'EPaperController@store')->name('epaper.resource.store');
			    Route::delete('form', 'EPaperController@destroy')->name('epaper.resource.delete');


			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'EPaperController@serviceMaster')->middleware('can:menu-epaper')->name('epaper.resource.service-master');
				    Route::get('subscription', 'SubscriptionController@serviceMaster')->middleware('can:menu-epaper')->name('epaper.subscription.service-master');
				    Route::post('subscription/get-data', 'SubscriptionController@getData')->middleware('can:menu-epaper')->name('epaper.subscription.service-master');
			    });

			    Route::get('subscription', 'SubscriptionController@index')->middleware('can:menu-epaper')->name('epaper.subscription.master');
	        
	        /*=====  End of EPaper CMS  ======*/


		});

		Route::group(['prefix' => 'epaper-package'], function() {
	        /*=============================================
	        =            EPaper CMS            =
	        =============================================*/
	        
			    Route::get('/', 'PackageController@index')->middleware('can:menu-epaper')->name('epaper-package.resource.master');
			    Route::get('form', 'PackageController@create')->name('epaper-package.resource.create');
			    Route::post('form', 'PackageController@store')->middleware('can:create-epaper')->name('epaper-package.resource.store');
			    Route::put('form', 'PackageController@store')->name('epaper-package.resource.store');
			    Route::delete('form', 'PackageController@destroy')->name('epaper-package.resource.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'PackageController@serviceMaster')->middleware('can:menu-epaper')->name('epaper-package.resource.service-master');
			    });
	        
	        /*=====  End of EPaper CMS  ======*/
		});

        
	});
});
