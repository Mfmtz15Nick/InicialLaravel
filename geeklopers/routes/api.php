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

	Route::get('clientes/{id}/eventos', 'ClientesController@eventos');
	Route::get('clientes/{id}/eventos/nuevo', 'ClientesController@eventosNuevo');
	Route::post('clientes/{id}/eventos', 'ClientesController@eventosStore');
	Route::get('clientes/eventos/{id}', 'ClientesController@eventoGetById');
	Route::put('clientes/eventos/{id}', 'ClientesController@eventoUpdate');
	Route::delete('clientes/eventos/{id}', 'ClientesController@eventoDelete');



// EVENTOS
	Route::resource('eventos', 'EventosController');
	Route::post('eventos/upload', 'EventosController@upload');
	Route::get('eventos/{id}/imagenes', 'EventosController@indexImagenes');
	Route::post('eventos/{id}/imagenes/store', 'EventosController@storeImagenes');
	Route::delete('eventos/{id}/imagenes', 'EventosController@destroyImagenes');
	Route::post('eventos/imagenes/{id}/ordenar','EventosController@ordenarImagenes');

