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

		Route::group(['prefix' => 'ads'], function() {

			Route::group(['prefix' => 'terms'], function() {
		        /*=============================================
		        =            Ads Terms CMS            =
		        =============================================*/
		        
				    Route::get('master', 'MstAdsController@index')->name('ads.terms')->middleware('can:menu-terms-ads');
				    Route::get('form', 'MstAdsController@create')->name('ads.terms');
				    Route::post('form', 'MstAdsController@store')->name('ads.terms')->middleware('can:create-terms-ads');
				    Route::put('form', 'MstAdsController@store')->name('ads.terms');
				    Route::delete('form', 'MstAdsController@destroy')->name('ads.terms');

				    Route::group(['prefix' => 'api'], function() {
					    Route::get('master', 'MstAdsController@serviceMaster')->middleware('can:menu-terms-ads');
				    });
		        
		        /*=====  End of Ads Terms CMS  ======*/
			});

			Route::group(['prefix' => 'iklan-umum-khusus'], function() {
				Route::group(['prefix' => 'categories'], function() {
			        /*=============================================
			        =            Ads Categories CMS            =
			        =============================================*/
			        
					    Route::get('master', 'MstAdCategoriesController@index')->name('ads.classic.categories')->middleware('can:menu-master-ads');
					    Route::get('form', 'MstAdCategoriesController@create')->name('ads.classic.categories');
					    Route::post('form', 'MstAdCategoriesController@store')->name('ads.classic.categories')->middleware('can:create-master-ads');
					    Route::put('form', 'MstAdCategoriesController@store')->name('ads.classic.categories');
					    Route::delete('form', 'MstAdCategoriesController@destroy')->name('ads.classic.categories');

					    Route::group(['prefix' => 'api'], function() {
						    Route::get('master', 'MstAdCategoriesController@serviceMaster')->middleware('can:menu-master-ads');
						    Route::get('subcategory', 'MstAdCategoriesController@subCategory');
					    });
			        
			        /*=====  End of Ads Categories CMS  ======*/
				});

				Route::group(['prefix' => 'pricelist'], function() {
			        /*=============================================
			        =            Ads Categories CMS            =
			        =============================================*/
			        
					    Route::get('master', 'RltAdsCategoriesController@index')->name('ads.classic.pricelist')->middleware('can:menu-master-ads');
					    Route::get('form', 'RltAdsCategoriesController@create')->name('ads.classic.pricelist');
					    Route::post('form', 'RltAdsCategoriesController@store')->name('ads.classic.pricelist')->middleware('can:create-master-ads');
					    Route::put('form', 'RltAdsCategoriesController@store')->name('ads.classic.pricelist');
					    Route::delete('form', 'RltAdsCategoriesController@destroy')->name('ads.classic.pricelist');

					    Route::group(['prefix' => 'api'], function() {
						    Route::get('master', 'RltAdsCategoriesController@serviceMaster')->middleware('can:menu-master-ads');
					    });
			        
			        /*=====  End of Ads Categories CMS  ======*/
				});

				Route::group(['prefix' => 'transaction'], function() {
				     /*=============================================
			        =            Ads Transaction CMS            =
			        =============================================*/
			        
					    Route::get('master', 'TrxAdsClassicController@index')->name('ads.classic.transaction');
					    Route::post('validation', 'TrxAdsClassicController@validation')->name('ads.classic.transaction')->middleware('can:create-transaction-classic-ads');
					    Route::put('validation', 'TrxAdsClassicController@validation')->name('ads.classic.transaction')->middleware('can:create-transaction-classic-ads');
					    Route::post('form', 'TrxAdsClassicController@store')->name('ads.classic.transaction')->middleware('can:create-transaction-classic-ads');
					    Route::put('form', 'TrxAdsClassicController@store')->name('ads.classic.transaction');
					    Route::delete('form', 'TrxAdsClassicController@destroy')->name('ads.classic.transaction');
					    Route::delete('force-delete', 'TrxAdsClassicController@forceDelete')->name('ads.classic.transaction')->middleware('can:super-access');
					    
					    Route::group(['middleware' => 'ads.transaction'], function() {
						    Route::get('form', 'TrxAdsClassicController@create')->name('ads.classic.transaction.create')->middleware('can:create-transaction-classic-ads');
						    Route::get('payment', 'TrxAdsClassicController@payment')->name('ads.classic.transaction.payment');
						    Route::post('payment', 'TrxAdsClassicController@payment')->name('ads.classic.transaction.payment');
					    });
					    
					    Route::get('invoice', 'TrxAdsClassicController@invoice')->name('ads.classic.transaction.invoice');
					    Route::get('report', 'TrxAdsClassicController@report')->name('ads.classic.transaction.report');
					    Route::get('approving-payment', 'TrxAdsClassicController@approvingPayment')->name('ads.classic.transaction.approving.payment');
					    Route::post('approving-payment', 'TrxAdsClassicController@approvingPayment')->name('ads.classic.transaction.approving.payment');
					    Route::put('approving-payment', 'TrxAdsClassicController@approvingPayment')->name('ads.classic.transaction.approving.payment');
					    Route::get('approving-content', 'TrxAdsClassicController@approvingContent')->name('ads.classic.transaction.approving.content');
					    Route::post('approving-content', 'TrxAdsClassicController@approvingContent')->name('ads.classic.transaction.approving.content');
					    Route::put('approving-content', 'TrxAdsClassicController@approvingContent')->name('ads.classic.transaction.approving.content');
					    Route::delete('delete-content', 'TrxAdsClassicController@deleteContent')->name('ads.classic.transaction.delete.content');
					    Route::delete('delete-schedule', 'TrxAdsClassicController@deleteSchedule')->name('ads.classic.transaction.delete.schedule');
					    Route::get('layouting-content', 'TrxAdsClassicController@layotingContent')->name('ads.classic.transaction.layouting.content');
					    Route::post('layouting-content', 'TrxAdsClassicController@layotingContent')->name('ads.classic.transaction.layouting.content');
					    Route::put('layouting-content', 'TrxAdsClassicController@layotingContent')->name('ads.classic.transaction.layouting.content');
					    Route::get('layouting-print', 'TrxAdsClassicController@layotingPrint')->name('ads.classic.transaction.layouting.content');

					    /*===================================
					    =            Bulk Action            =
					    ===================================*/
					    
						    Route::get('bulk-approving-content', 'TrxAdsClassicController@bulkApprovingContent')->name('ads.classic.transaction.approving.content')->middleware('can:layouting-content-classic-ads');
					    
					    /*=====  End of Bulk Action  ======*/
					    

					    Route::group(['prefix' => 'api'], function() {
						    Route::get('master', 'TrxAdsClassicController@serviceMaster');
					    });
			        
			        /*=====  End of Ads Transaction CMS  ======*/
				});

			});

			Route::group(['prefix' => 'iklan-web'], function() {
				Route::group(['prefix' => 'pricelist'], function() {
			        /*=============================================
			        =            Ads Categories CMS            =
			        =============================================*/
			        
					    Route::get('master', 'MstAdsWebController@index')->name('ads.web.pricelist')->middleware('can:menu-master-ads');
					    Route::get('form', 'MstAdsWebController@create')->name('ads.web.pricelist');
					    Route::post('form', 'MstAdsWebController@store')->name('ads.web.pricelist')->middleware('can:create-master-ads');
					    Route::put('form', 'MstAdsWebController@store')->name('ads.web.pricelist');
					    Route::delete('form', 'MstAdsWebController@destroy')->name('ads.web.pricelist');

					    Route::group(['prefix' => 'api'], function() {
						    Route::get('master', 'MstAdsWebController@serviceMaster')->middleware('can:menu-master-ads');
					    });
			        
			        /*=====  End of Ads Categories CMS  ======*/
				});

				Route::group(['prefix' => 'transaction'], function() {
				     /*=============================================
			        =            Ads Transaction CMS            =
			        =============================================*/
			        
					    Route::get('master', 'TrxAdsWebController@index')->name('ads.web.transaction');
					    Route::post('form', 'TrxAdsWebController@store')->name('ads.web.transaction')->middleware('can:create-transaction-web-ads');
					    Route::put('form', 'TrxAdsWebController@store')->name('ads.web.transaction');
					    Route::delete('form', 'TrxAdsWebController@destroy')->name('ads.web.transaction');
					    
					    Route::group(['middleware' => 'ads.transaction'], function() {
						    Route::get('form', 'TrxAdsWebController@create')->name('ads.web.transaction.create')->middleware('can:create-transaction-web-ads');
						    Route::get('payment', 'TrxAdsWebController@payment')->name('ads.web.transaction.payment');
					    });
					    
					    Route::get('invoice', 'TrxAdsWebController@invoice')->name('ads.web.transaction.invoice');
					    Route::get('report', 'TrxAdsWebController@report')->name('ads.web.transaction.report');
					    Route::get('approving-payment', 'TrxAdsWebController@approvingPayment')->name('ads.web.transaction.approving.payment');
					    Route::post('approving-payment', 'TrxAdsWebController@approvingPayment')->name('ads.web.transaction.approving.payment');
					    Route::get('approving-content', 'TrxAdsWebController@approvingContent')->name('ads.web.transaction.approving.content');
					    Route::post('approving-content', 'TrxAdsWebController@approvingContent')->name('ads.web.transaction.approving.content');
					    Route::get('layouting-content', 'TrxAdsWebController@layotingContent')->name('ads.web.transaction.layouting.content');
					    Route::post('layouting-content', 'TrxAdsWebController@layotingContent')->name('ads.web.transaction.layouting.content');

					    Route::group(['prefix' => 'api'], function() {
						    Route::get('master', 'TrxAdsWebController@serviceMaster');
					    });
			        
			        /*=====  End of Ads Transaction CMS  ======*/
				});
			});


		});
        
	});
});
