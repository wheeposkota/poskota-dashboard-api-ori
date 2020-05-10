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

		Route::group(['prefix' => 'agent'], function() {

	        /*=============================================
	        =            Member CMS                       =
	        =============================================*/
	        
			    Route::get('/', 'AgentController@index')->middleware('can:menu-agent')->name('agent.master');
			    Route::get('form', 'AgentController@create')->name('agent.create');
			    Route::post('form', 'AgentController@store')->middleware('can:create-agent')->name('agent.store');
			    Route::put('form', 'AgentController@store')->name('agent.store');
			    Route::delete('form', 'AgentController@destroy')->name('agent.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'AgentController@serviceMaster')->middleware('can:menu-agent')->name('agent.service-master');
			    });
	        
	        /*=====  End of Member CMS  ======*/


		});
        
	});
});