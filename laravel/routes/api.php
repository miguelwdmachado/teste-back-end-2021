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

/*'middleware' => 'api',
'namespace' => 'App\Http\Controllers',
'prefix' => 'auth'*/
Route::post('auth/login', 'App\Http\Controllers\AuthController@login');

Route::group([
  'middleware' => ['jwt.verify'],
  'namespace' => 'App\Http\Controllers',
  'prefix' => 'auth'
], function() {

    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');

    Route::post('inc_prod', 'ProductsController@store');
    Route::post('edi_prod', 'ProductsController@update');
    Route::post('exc_prod', 'ProductsController@destroy');
    Route::post('lis_prod', 'ProductsController@index');

});
