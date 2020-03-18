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

Route::post('login', 'Api\AuthApiController@login');
Route::post('register', 'Api\AuthApiController@register');
Route::post('logout', 'Api\AuthApiController@logout');

Route::group(['middleware' => 'auth:api'], function(){

    Route::resource('catalog', 'Api\CatalogApiController');
    Route::post('catalog/category', 'Api\CatalogApiController@getCategoriesByCatalog');
    Route::post('catalog/category/item', 'Api\CatalogApiController@getItemsByCategory');
    Route::get('catalog/category/item/{uuid}', 'Api\CatalogApiController@getItem');

    Route::resource('entity', 'Api\EntityApiController');
    Route::get('entity/getEstimate/{uuid}', 'Api\EntityApiController@getEstimate');
    Route::get('entity/getEstimatePDF/{uuid}', 'Api\EntityApiController@getEstimatePDF');

    Route::resource('node', 'Api\NodeApiController');
    Route::post('node/copy', 'Api\NodeApiController@copyNode');
    Route::delete('node/destroyItemFromNode/{uuid}', 'Api\NodeApiController@destroyItemFromNode');

    Route::post('addItemToNode', 'Api\NodeApiController@addItemToNode');

    Route::get('test', 'Api\AuthApiController@test');
    
});

Route::delete('node/destroyItemFromNode/{uuid}', 'Api\NodeApiController@destroyItemFromNode');