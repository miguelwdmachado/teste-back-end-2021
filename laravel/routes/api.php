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

Route::group([

    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::get('logout', 'AuthController@sair');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@show');

    Route::post('inc_prod', 'ProductsController@store');
    Route::post('edi_prod', 'ProductsController@update');
    Route::delete('exc_prod', 'ProductsController@destroy');
    Route::get('lis_prod', 'ProductsController@index');

});
