<?php
use Illuminate\Http\Request;

// LOGIN
	Route::post('/upload', 'ConfiguracionController@upload');
	Route::get('login/logout', 'LoginController@logout');
	Route::get('login/check', 'LoginController@check');

// USUARIOS
	Route::resource('usuarios', 'UsuariosController');
	Route::get('usuarios/{nombre}/buscar', 'UsuariosController@buscarByNombreOrApellidoOrIdRol');
	Route::get('usuarios/{nombre}/{idRol}/buscar', 'UsuariosController@buscarByNombreOrApellidoOrIdRol');
	Route::get('usuarios/{idRol}/buscarPorRol', 'UsuariosController@buscarByIdRol');

// TIPOSEVENTOS
	Route::resource('tiposEventos', 'TiposEventosController');
	Route::post('tiposEventos/upload', 'TiposEventosController@upload');
	
// CLIENTES
	Route::resource('clientes', 'ClientesController');
	