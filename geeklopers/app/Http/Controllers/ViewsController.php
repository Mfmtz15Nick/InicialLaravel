<?php

namespace App\Http\Controllers;
use Session;
use Request;
use Log;
use Exception;

class ViewsController extends Controller
{

	/**
	 * Show the application login screen to the user.
	 *
	 * @return Response
	 */
	public function inicio()
	{
		// if (Session::has('auth.usuarioPublico')) {
			// return redirect('admin');
		// } else {
			return view('inicio');
		// }
	}

	/* ADMINISTRATOR */

	/**
	 * Show the application login screen to the user.
	 *
	 * @return Response
	 */
	public function login()
	{
		// if (Session::has('auth.usuarioPublico')) {
			// return redirect('admin');
		// } else {
			return view('login');
		// }
	}

	/**
	 * Show the application admin screen to the user.
	 *
	 * @return Response
	 */
	public function admin()
	{
		// if (Session::has('auth.usuarioPublico')) {
			return view('admin');
		// } else {
		//  return redirect('login');
		// }
	}
}
