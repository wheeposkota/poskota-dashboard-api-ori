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

		Route::group(['prefix' => 'commodity'], function() {

	        /*=============================================
	        =            Commodity Update CMS            =
	        =============================================*/
	        
			    Route::get('/', 'CommodityUpdateController@index')->middleware('can:menu-member')->name('commodity.resource.master');
			    Route::delete('form', 'CommodityUpdateController@destroy')->name('commodity.resource.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'CommodityUpdateController@serviceMaster')->middleware('can:menu-member')->name('commodity.resource.service-master');
				    Route::post('get-data', 'CommodityUpdateController@getData')->middleware('can:menu-member')->name('commodity.resource.service-master');
			    });
	        
	        /*=====  End of Commodity Update CMS  ======*/


		});

		Route::group(['prefix' => 'commodity-categories'], function() {
	        /*=============================================
	        =            Commodity Category CMS            =
	        =============================================*/
	        
			    Route::get('/', 'CommodityCategoriesController@index')->middleware('can:menu-commodity')->name('commodity.categories.master');
			    Route::get('form', 'CommodityCategoriesController@create')->name('commodity.categories.create');
			    Route::post('form', 'CommodityCategoriesController@store')->middleware('can:create-commodity')->name('commodity.categories.store');
			    Route::put('form', 'CommodityCategoriesController@store')->name('commodity.categories.store');
			    Route::delete('form', 'CommodityCategoriesController@destroy')->name('commodity.categories.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'CommodityCategoriesController@serviceMaster')->middleware('can:menu-commodity')->name('commodity.categories.service-master');
			    });
	        
	        /*=====  End of Commodity Category CMS  ======*/
		});

        
	});
});
