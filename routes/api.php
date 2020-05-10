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

Route::get('/', 'Controller@home');
Route::get('version', 'Controller@version');

Route::post('auth/register', 'AuthEndpoint@register');
Route::post('auth/login', 'AuthEndpoint@login');

Route::post('subs_topic', 'Controller@subsTopic');

Route::group(['prefix' => 'dev'], function() {
    Route::any('callfcm', 'Controller@callFcm');
});

Route::group(['middleware' => ['auth']], function () {
    Route::post('auth/refresh_token', 'AuthEndpoint@refresh_jwt');
    Route::get('auth/profile', 'AuthEndpoint@profile');
    Route::post('auth/profile', 'AuthEndpoint@profilePost');
    Route::post('auth/change_password', 'AuthEndpoint@change_password');
});

// post
Route::get('category', 'PostEndpoint@category');
Route::get('post_list', 'PostEndpoint@post_list');
Route::get('post_detail', 'PostEndpoint@post_detail');
Route::get('post_related', 'PostEndpoint@post_related');

// bookmark
Route::group(['middleware' => ['auth'], 'prefix' => 'bookmark'], function () {
    Route::get('/', 'PostEndpoint@retrieve_bookmark');
    Route::post('/', 'PostEndpoint@submit_bookmark');
});

// media
Route::get('media_list', 'MediaEndpoint@media_list');
Route::get('media_detail', 'MediaEndpoint@media_detail');

// epaper
Route::get('epaper_package', 'EpaperEndpoint@epaper_package');
Route::group(['middleware' => ['auth']], function () {
    Route::get('epaper_check', 'EpaperEndpoint@epaper_check');
    Route::get('epaper_list', 'EpaperEndpoint@epaper_list');
    Route::get('epaper_history_payment', 'EpaperEndpoint@epaper_history_payment');
    Route::get('epaper_detail', 'EpaperEndpoint@epaper_detail');
});

// mix
Route::get('other_page', 'PostEndpoint@other_page');
Route::get('other_page_detail', 'PostEndpoint@other_page_detail');

// comment
Route::get('comment_list', 'CommentEndpoint@comment_list');
Route::group(['middleware' => ['auth']], function () {
    Route::post('comment_post', 'CommentEndpoint@comment_post');
});

Route::get('flush', 'Controller@cacheFlush');
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::group(['prefix' => 'fileasset'], function () {
    Route::get('{any?}', 'Controller@assetStatic')->where('any', '.*');;
});

Route::group(['prefix' => 'payment'], function () {
    //client
    Route::group(['middleware' => ['auth']], function () {
        Route::get('bank_account', 'PaymentEndpoint@bankAccount');
        Route::post('checkout', 'PaymentEndpoint@checkout');
        Route::post('checkout_gopay', 'PaymentEndpoint@checkoutGopay');
    });

    //h2h midtrans
    Route::post('notification', 'IntegrationEndpoint@notification');
});

Route::get('advertisement', 'AdEndpoint@advertisement');
Route::get('advertisement_submit_created', 'AdEndpoint@advertisementCreated')->middleware('auth');
Route::post('advertisement_submit_confirmation', 'AdEndpoint@advertisementConfirmation')->middleware('auth');
Route::get('advertisement_submit', 'AdEndpoint@advertisementParameter')->middleware('auth');
Route::post('advertisement_submit', 'AdEndpoint@advertisementStore')->middleware('auth');
Route::get('advertisement_grouped', 'AdEndpoint@advertisementGrouped');
Route::get('advertisement_category', 'AdEndpoint@advertisementCategory');
Route::get('banner', 'AdEndpoint@banner');
Route::get('banner_all', 'AdEndpoint@bannerAll');

//commodity
Route::get('commodity_public', 'CommodityEndpoint@published');
Route::group(['prefix' => 'manage_commodity', 'middleware' => ['auth']], function () {
    Route::get('/', 'CommodityEndpoint@index');
    Route::get('type', 'CommodityEndpoint@type');
    Route::post('create', 'CommodityEndpoint@create');
    Route::post('update', 'CommodityEndpoint@update');
    Route::post('delete', 'CommodityEndpoint@delete');
});

// Route::group(['prefix' => 'manage_adv', 'middleware' => ['auth']], function () {
//     Route::get('/', 'AdvertisementEndpoint@index');
//     Route::get('type', 'AdvertisementEndpoint@type');
//     Route::post('create', 'AdvertisementEndpoint@create');
//     Route::post('update', 'AdvertisementEndpoint@update');
//     Route::post('delete', 'AdvertisementEndpoint@delete');
// });
