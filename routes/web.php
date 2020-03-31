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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/estimate', function () {
    return view('pdf.estimateTest');
});

Route::post('/uploadcatalog', 'Web\CatalogController@uploadCatalog')->name('uploadCatalog');
Route::post('catalog/getCategories', 'Web\CatalogController@getCategories')->name('getCategories');
Route::post('catalog/getItems', 'Web\CatalogController@getItems')->name('getItems');
Route::post('catalog/deleteCatalog', 'Web\CatalogController@deleteCatalog')->name('deleteCatalog');
Route::post('catalog/renameCatalog', 'Web\CatalogController@renameCatalog')->name('renameCatalog');
Route::post('catalog/deleteCategory', 'Web\CatalogController@deleteCategory')->name('deleteCategory');
Route::post('catalog/renameCategory', 'Web\CatalogController@renameCategory')->name('renameCategory');