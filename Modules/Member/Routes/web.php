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

		Route::group(['prefix' => 'member'], function() {

	        /*=============================================
	        =            Member CMS                       =
	        =============================================*/
	        
			    Route::get('/', 'MemberController@index')->middleware('can:menu-member')->name('member.master');
			    Route::get('form', 'MemberController@create')->name('member.create');
			    Route::post('form', 'MemberController@store')->middleware('can:create-member')->name('member.store');
			    Route::put('form', 'MemberController@store')->name('member.store');
			    Route::delete('form', 'MemberController@destroy')->name('member.delete');

			    Route::group(['prefix' => 'api'], function() {
				    Route::get('master', 'MemberController@serviceMaster')->middleware('can:menu-member')->name('member.service-master');
				    Route::post('get-data', 'MemberController@getData')->middleware('can:menu-member')->name('member.service-master');
			    });
	        
	        /*=====  End of Member CMS  ======*/


		});
        
	});
});