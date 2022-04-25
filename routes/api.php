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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//User
Route::get('/users', 'Api\UserController@index');
Route::get('/user/{id}', 'Api\UserController@show');
Route::post('/user/save', 'Api\UserController@store');
Route::get('/user/delete/{id}', 'Api\UserController@delete');


//Expense
Route::post('/expense/save', 'Api\ExpenseController@store');
Route::get('/expenses/{id}', 'Api\ExpenseController@expenses');

//Owes
Route::get('/owes/{id}', 'Api\OweController@index');

