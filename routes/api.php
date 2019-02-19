<?php
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
Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::get('verifyemail/{token}', 'API\UserController@verify');
Route::group(['middleware' => 'auth:api'], function() {
    Route::get('details', 'API\UserController@details');
    Route::post('logout','API\UserController@logoutApi');
    Route::post('userimagedelete/{id}','API\UserController@userimagedelete');
    Route::post('updateuser/{id}','API\UserController@updateuser');
});


Route::get('showall/{user_id}','UploadController@showall');
Route::get('show/{id}','UploadController@show');
Route::post('store/{id}','UploadController@store');
Route::post('update/{id}','UploadController@update');
Route::delete('delete/{id}','UploadController@delete');

