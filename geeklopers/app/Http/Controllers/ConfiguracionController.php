<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;
use Session;

class ConfiguracionController extends Controller {

	protected $usuario;


	/**
	 * Create a new controller instance.
	 *
	 * @param  UserRepository  $usuario
	 * @return void
	 */
	public function __construct()
	{
		// Obtenemos el Usuario en Sesion
		$this->usuario = Session::get('auth.usuario');

	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function upload()
	{
		try {

			$file = Request::file('file');
			//$tempPath = $file['tmp_name'];
			$vc_imagen = $this->usuario->id .'_'. $file->getClientOriginalName();
			//$uploadPath = IMAGES_PATH . 'temp' . DIRECTORY_SEPARATOR . $vc_imagen;

			//if( !move_uploaded_file($tempPath, $uploadPath) ){
			//	throw new Exception("Error Processing Request");
			//}
			//else{
			//	Response::json(['estatus'=>true, 'nombre' => $vc_imagen ]);
			//}

			$file->move( public_path('images/temp'), $vc_imagen);

			return [
				'estatus' 	=> true,
				'nombre'	=> $vc_imagen
			];
		} catch (Exception $e) {
			return Response::json(['estatus'=>false, 'message' => $e->getLine() ],418);
		}
	}

}
