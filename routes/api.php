<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['prefix' => 'v1'], function () {
    Route::post('auth/register', 'UserController@register');
    Route::post('auth/login', 'UserController@login');
    Route::get('product', 'ProductsController@index');
    Route::get('product/{id}', 'ProductsController@show');
    //auth Request 
    Route::group(['middleware' => ['auth']], function (){
        
        //User
        Route::apiResource('user', 'UserController');
        Route::put('profile/user/{id}', 'UserController@updateProfile');
        Route::get('profile/user', 'UserController@me');
        
        //Rules
        Route::apiResource('rule', 'RulesController');
        
        //Products
        Route::apiResource('product', 'ProductsController')->except(['index', 'show']);;

    });
});