<?php
use Illuminate\Http\Request;

// LOGIN
	Route::post('/upload', 'ConfiguracionController@upload');
	Route::get('login/logout', 'LoginController@logout');
	Route::get('login/check', 'LoginController@check');

// USUARIOS
	Route::resource('usuarios', 'UsuariosController');
	