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


// LOGIN

	Route::post('/upload', 'ConfiguracionController@upload');
	Route::get('login/logout', 'LoginController@logout');
	Route::get('login/check', 'LoginController@check');

// MENU

	Route::resource('usuarios', 'UsuariosController');
	Route::get('usuarios/{nombre}/buscar', 'UsuariosController@buscarByNombreOrApellido');
	Route::get('usuarios/{idRol}/buscarPorRol', 'UsuariosController@buscarByIdRol');
