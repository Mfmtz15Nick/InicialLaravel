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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'ViewsController@inicio');
Route::get('inicio', 'ViewsController@inicio');
Route::get('admin', 'ViewsController@admin');
Route::get('login', 'ViewsController@login');

/*
|--------------------------------------------------------------------------
| Api fuera de Token
|--------------------------------------------------------------------------
*/

Route::post('api/login', 'LoginController@login');
