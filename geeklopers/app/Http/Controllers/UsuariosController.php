<?php namespace App\Http\Controllers;

use DB;
use Log;
use Auth;
use Hash;
use Request;
use Session;
use Response;
use Exception;
use Validator;
use JWTFactory;
use JWTAuth;
use Carbon\Carbon;
use App\Models\Usuarios;
use App\Models\UsuariosRoles;
use App\Models\UsuariosDetalles;
use App\Models\UsuariosTokens;
use App\Models\Generos;
use App\Models\Roles;

class UsuariosController extends Controller
{
	const SISTEMA 				= 1;
	const ADMINISTRADOR 	= 2;
	const ACTIVO 					= 1;
	const INACTIVO 				= 0;
	const DATOSXPAGINA 		= 10;
	/**
     * Validate a new controller instance.
     *
     * @param  UserRepository  $usuario
     * @return void
     */
	public function validateController($usuario)
	{
		// Validar parametros de session necesarios
    if (!isset($usuario->rol->id)) {
        return false;
    }

		// Validar acceso permitido por Roles
		if ( $usuario->rol->id_rol != self::ADMINISTRADOR ) {
        return false;
		}

		return true;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
    public function index()
    {
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			// Obtener usuarios del sistema
			$usuarios = Usuarios::join('usuariosDetalles as UD', 'usuarios.id', '=', 'UD.id_usuario')
						->join('usuariosRoles as UR', 'usuarios.id', '=', 'UR.id_usuario')
						->join('roles as R', 'UR.id_rol', '=', 'R.id')
						->where([
								['usuarios.sn_activo',    self::ACTIVO],
								['usuarios.sn_eliminado', self::INACTIVO],
								['UD.sn_activo',      	 	self::ACTIVO],
								['UD.sn_eliminado',   	 	self::INACTIVO],
								['UR.sn_activo',      	 	self::ACTIVO],
								['UR.sn_eliminado',   	 	self::INACTIVO],
								['R.sn_activo',      	 		self::ACTIVO],
								['R.sn_eliminado',   	 		self::INACTIVO],
								['R.id',   	 							'!=', self::SISTEMA]
						])
						->whereNull('usuarios.dt_eliminado')
						->whereNull('UD.dt_eliminado')
						->whereNull('UR.dt_eliminado')
						->whereNull('R.dt_eliminado')
						->selectRaw('usuarios.id, UD.vc_nombre, UD.vc_apellido, UD.vc_email, R.vc_nombre as vc_nombreRol')
						->paginate(self::DATOSXPAGINA);

				$roles 		= Roles::Filtro()
		 												->where('id', '!=', self::SISTEMA)
		 												->selectRaw('id, vc_nombre')
		 												->orderBy('vc_nombre')
		 												->get();

				return [ 'usuarios' => $usuarios, 'roles' => $roles ];
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		// Verificación para el uso del Controllador
		$body = (Object)Request::all();
		if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
		}

		// Obtener todos los Sexos
		$generos 	= Generos::Filtro()->selectRaw('id, vc_nombre')->orderBy('vc_nombre')->get();
		$roles 		= Roles::Filtro()
												->where('id', '!=', self::SISTEMA)
												->selectRaw('id, vc_nombre')
												->orderBy('vc_nombre')
												->get();
		return [
			'roles' 	=> $roles,
			'generos' => $generos,
		];
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Verificación para el uso del Controllador
		$body = (Object)Request::all();
		if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
		}

    // Validacion de parametros
    $validator = Validator::make((array)$body, [
        'id_genero'      		=> 'required',
		'id_rol'      			=> 'required',
        'vc_nombre'      		=> 'required',
        'vc_apellido'    		=> 'required',
        'vc_email'       		=> 'required',
        'vc_password'    		=> 'required',
        'vc_password_re'        => 'required'
    ]);

    if ($validator->fails()) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
    }

    // Validar contraseñas
    if ($body->vc_password != $body->vc_password_re) {
        return Response::json(['texto' => 'Las contraseñas proporcionadas no son identicas.'], 418);
		}

		$tmp_imagen 	= [];
		$ext 					= [];
		$folder     	= 'images/usuarios/';

    try
    {
        DB::beginTransaction();

        // Verifica si existe en la BD
        if( UsuariosDetalles::Filtro()->EsEmail($body->vc_email)->exists() ){
          throw new Exception('El correo '. $body->vc_email .', ya se encuentra registrado.', 418);
        }
				else {
      		// Crear Usuario
					$usuario = Usuarios::create([
						'id_creador' 	=> $body->usuario->id
					]);

					// Crear Usuario Roles
        	UsuariosRoles::create([
        		'id_usuario' 	=> $usuario->id,
        		'id_rol'	 		=> $body->id_rol,
        		'id_creador' 	=> $body->usuario->id
        	]);

        	// Crear Usuario Detalles
        	$usuarioDetalle = UsuariosDetalles::create([
        		'id_usuario' 	=> $usuario->id,
        		'id_genero' 	=> $body->id_genero,
        		'vc_nombre' 	=> $body->vc_nombre,
        		'vc_apellido' => $body->vc_apellido,
        		'vc_email' 		=> $body->vc_email,
        		'vc_password' => $body->vc_password,
        		'id_creador' 	=> $body->usuario->id
        	]);
					// --------------- GUARDAR LA IMAGEN ---------------
					// Primera Imagen
					$tmp_imagen = public_path( $folder . $body->vc_imagen);
					$ext = pathinfo( $tmp_imagen, PATHINFO_EXTENSION);
					$vc_imagen = 'Imagen_U_'. $usuario->id .'_'. Carbon::now()->timestamp .'.'. $ext;

					copy( $tmp_imagen, public_path( $folder . $vc_imagen ) );
					unlink($tmp_imagen);

					$usuarioDetalle->vc_imagen 			= $vc_imagen;
					$usuarioDetalle->vc_imagenUrl 	= $folder . $vc_imagen;
					$usuarioDetalle->save();
					// -------------------------------------------------
        }

        DB::commit();
        return ['texto' => 'El usuario '. $body->vc_nombre .' '.$body->vc_apellido.', fue guardado correctamente.'];
    }
    catch (Exception $e)
    {
        DB::rollBack();
        return Response::json(['texto' => $e->getMessage()], 418);
    }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// Verificación para el uso del Controllador
		$body = (Object)Request::all();
		if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		// Verificación para el uso del Controllador
		$body = (Object)Request::all();
		if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
		}

		// Obtener Usuario
		$usuario = Usuarios::Filtro()->with('detalle', 'rol')->findOrFail($id);
		$usuario = [
			'id' 					=> $usuario->id,
			'vc_nombre' 	=> $usuario->detalle->vc_nombre,
			'vc_apellido' => $usuario->detalle->vc_apellido,
			'id_genero' 	=> $usuario->detalle->id_genero,
			'id_rol' 			=> $usuario->rol->id_rol,
			'vc_email' 		=> $usuario->detalle->vc_email,
			'vc_password' => $usuario->detalle->vc_password,
			'vc_imagen' 	=> $usuario->detalle->vc_imagen,
			'vc_imagenUrl' => $usuario->detalle->vc_imagenUrl
		];

		return $usuario;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// Verificación para el uso del Controllador
		$body = (Object)Request::all();
		if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
		}

    // Validacion de parametros
    $validator = Validator::make((array)$body, [
        'id_genero'      		=> 'required',
				'id_rol'      			=> 'required',
        'vc_nombre'      		=> 'required',
        'vc_apellido'    		=> 'required',
        'vc_email'       		=> 'required',
        'vc_password'    		=> 'required',
        'vc_password_re'    => 'required'
    ]);

    if ($validator->fails()) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
    }

    // Validar contraseñas
    if ($body->vc_password != $body->vc_password_re) {
        return Response::json(['texto' => 'Las contraseñas proporcionadas no son identicas.'], 418);
    }

		$tmp_imagen 	= [];
		$ext 					= [];
		$folder     	= 'images/usuarios/';

    try
    {
        DB::beginTransaction();

        //Verifica si existe el correo en la BD
        if( UsuariosDetalles::Filtro()->EsEmail($body->vc_email)->where('id_usuario', '!=', $id)->exists() ){
            throw new Exception('El correo '. $body->vc_email .', ya se encuentra registrado.', 418);
        } else {
						// Obtener el detalle del Usuario
				    $usuarioDetalleEliminar = UsuariosDetalles::Filtro()->where('id_usuario', $id)->first();
				    $usuarioDetalleEliminar->sn_activo 		= self::INACTIVO;
				    $usuarioDetalleEliminar->sn_eliminado = self::ACTIVO;
				    $usuarioDetalleEliminar->save();
			      $usuarioDetalleEliminar->delete();

          	// Crear nuevo detalle del Usuario
          	$usuarioDetalle = UsuariosDetalles::create([
          		'id_usuario' 			=> $id,
          		'id_genero' 			=> $body->id_genero,
          		'vc_nombre' 			=> $body->vc_nombre,
          		'vc_apellido' 		=> $body->vc_apellido,
          		'vc_email' 				=> $body->vc_email,
          		'vc_password' 		=> $body->vc_password,
          		'id_creador'			=> $body->usuario->id
						]);

						if ($body->vc_imagen != $usuarioDetalleEliminar->vc_imagen ) {
							// --------------- GUARDAR LA IMAGEN ---------------
							// Primera Imagen
							$tmp_imagen = public_path( $folder . $body->vc_imagen);
							$ext = pathinfo( $tmp_imagen, PATHINFO_EXTENSION);
							$vc_imagen = 'Imagen_U_'. $id .'_'. Carbon::now()->timestamp .'.'. $ext;

							copy( $tmp_imagen, public_path( $folder . $vc_imagen ) );
							unlink($tmp_imagen);

							$usuarioDetalle->vc_imagen 			= $vc_imagen;
							$usuarioDetalle->vc_imagenUrl 	= $folder . $vc_imagen;
							// Eliminamos la imagen anterior
							if ($usuarioDetalleEliminar->vc_imagen) {
								$imagen_anterior = public_path( $folder . $usuarioDetalleEliminar->vc_imagen);
								unlink($imagen_anterior);
							}
							// -------------------------------------------------
						}
						else {
							$usuarioDetalle->vc_imagen 			= $usuarioDetalleEliminar->vc_imagen;
							$usuarioDetalle->vc_imagenUrl 	= $usuarioDetalleEliminar->vc_imagenUrl;
						}
						$usuarioDetalle->save();
        }

        DB::commit();
        return ['texto' => 'El usuario '. $body->vc_nombre .' '.$body->vc_apellido.', fue actualizado correctamente.'];
    }
    catch (Exception $e)
    {
        DB::rollBack();
        return Response::json(['texto' => $e->getMessage()],418);
    }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function destroy($id)
    {
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();
			if (!$this->validateController($body->usuario)) {
				return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			$usuario_imagen = '';
			$folder     		= 'images/usuarios/';

			try
			{
				DB::beginTransaction();

				// Eliminar Usuario
				$usuario = Usuarios::Filtro()->findOrFail($id);
				$usuario->sn_activo 	 = self::INACTIVO;
				$usuario->sn_eliminado = self::ACTIVO;
				$usuario->save();
				$usuario->delete();

				// Eliminar Usuario Detalle
				$usuarioDetalle = UsuariosDetalles::Filtro()->where('id_usuario', $usuario->id)->first();
				$usuarioDetalle->sn_activo 	 	= self::INACTIVO;
				$usuarioDetalle->sn_eliminado = self::ACTIVO;
				$usuarioDetalle->save();
				$usuarioDetalle->delete();

				// Eliminar Usuario Rol
				$usuarioRol = UsuariosRoles::Filtro()->where('id_usuario', $usuario->id)->first();
				$usuarioRol->sn_activo 	 	= self::INACTIVO;
				$usuarioRol->sn_eliminado = self::ACTIVO;
				$usuarioRol->save();
				$usuarioRol->delete();

				// Eliminar Usuario Tokens
				$usuarioTokens = UsuariosTokens::Filtro()->where('id_usuario', $usuario->id)->get();
				foreach ($usuarioTokens as $usuarioToken) {
					$usuarioToken->sn_activo 	 	= self::INACTIVO;
					$usuarioToken->sn_eliminado = self::ACTIVO;
					$usuarioToken->save();
					$usuarioToken->delete();
				}

				// Se elimina la imagen
				if ($usuarioDetalle->vc_imagen) {
					$eliminarImagen = public_path( $folder . $usuarioDetalle->vc_imagen);
					unlink($eliminarImagen);
				}

				DB::commit();
				return ['texto' => 'El usuario '.$usuarioDetalle->vc_nombre.' '.$usuarioDetalle->vc_apellido.', fue eliminado correctamente.'];
			}
			catch (Exception $e)
			{
				DB::rollBack();
				return Response::json(['texto' => 'El usuario, no se pudo eliminar correctamente.'], 418);
			}
		}

		/**
		 * Display a listing of the resource.
		 *
		 * @return Response
		 */
			public function buscarByNombreOrApellidoOrIdRol($nombre, $idRol = null)
			{

				Log::info([
					'$nombre' => $nombre,
					'$idRol' 	=> $idRol
				]);

				// Verificación para el uso del Controllador
				$body = (Object)Request::all();

				if (!$this->validateController($body->usuario)) {
				return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
				}

				// Obtener usuarios del sistema
				$usuarios = Usuarios::join('usuariosDetalles as UD', 'usuarios.id', '=', 'UD.id_usuario')
							->join('usuariosRoles as UR', 'usuarios.id', '=', 'UR.id_usuario')
							->join('roles as R', 'UR.id_rol', '=', 'R.id')
              ->where([
                  ['usuarios.sn_activo',    self::ACTIVO],
                  ['usuarios.sn_eliminado', self::INACTIVO],
                  ['UD.sn_activo',      	 	self::ACTIVO],
                  ['UD.sn_eliminado',   	 	self::INACTIVO],
									['UR.sn_activo',      	 	self::ACTIVO],
                  ['UR.sn_eliminado',   	 	self::INACTIVO],
									['R.sn_activo',      	 		self::ACTIVO],
                  ['R.sn_eliminado',   	 		self::INACTIVO],
									['R.id',   	 							'!= ', self::SISTEMA]
              ])
              ->whereNull('usuarios.dt_eliminado')
              ->whereNull('UD.dt_eliminado')
							->whereNull('UR.dt_eliminado')
							->whereNull('R.dt_eliminado')
							->selectRaw('usuarios.id, UD.vc_nombre, UD.vc_apellido, UD.vc_email, R.vc_nombre as vc_nombreRol');

				if ( $idRol ) {
					$usuarios
						->where(function( $q ) use( $nombre, $idRol ){
								$q->where('UD.vc_nombre', 'like', '%'.$nombre.'%')->where('R.id' , $idRol);
						})
						->orWhere(function( $q ) use( $nombre, $idRol ){
								$q->where('UD.vc_apellido', 'like', '%'.$nombre.'%')->where('R.id' , $idRol);
						})
						->orWhere(function( $q ) use( $nombre, $idRol ){
								$q->where('UD.vc_email', 'like', '%'.$nombre.'%')->where('R.id' , $idRol);
						});
				}
				else{
					$usuarios
						->where(function( $q ) use( $nombre ){
								$q->where('UD.vc_nombre', 'like', '%'.$nombre.'%');
						})
						->orWhere(function( $q ) use( $nombre ){
								$q->where('UD.vc_apellido', 'like', '%'.$nombre.'%');
						})
						->orWhere(function( $q ) use( $nombre ){
								$q->where('UD.vc_email', 'like', '%'.$nombre.'%');
						});
				}

        $usuarios = $usuarios->paginate(self::DATOSXPAGINA);

				return ['usuarios' => $usuarios];
			}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
		public function buscarByIdRol($idRol)
		{
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
			return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			// Obtener usuarios del sistema
			$usuarios = Usuarios::join('usuariosDetalles as UD', 'usuarios.id', '=', 'UD.id_usuario')
						->join('usuariosRoles as UR', 'usuarios.id', '=', 'UR.id_usuario')
						->join('roles as R', 'UR.id_rol', '=', 'R.id')
						->where([
								['usuarios.sn_activo',    self::ACTIVO],
								['usuarios.sn_eliminado', self::INACTIVO],
								['UD.sn_activo',      	 	self::ACTIVO],
								['UD.sn_eliminado',   	 	self::INACTIVO],
								['UR.sn_activo',      	 	self::ACTIVO],
								['UR.sn_eliminado',   	 	self::INACTIVO],
								['R.sn_activo',      	 		self::ACTIVO],
								['R.sn_eliminado',   	 		self::INACTIVO],
								['R.id',   	 							$idRol],
						])
						->whereNull('usuarios.dt_eliminado')
						->whereNull('UD.dt_eliminado')
						->whereNull('UR.dt_eliminado')
						->whereNull('R.dt_eliminado')
						->selectRaw('usuarios.id, UD.vc_nombre, UD.vc_apellido, UD.vc_email, R.vc_nombre as vc_nombreRol')
						->paginate(self::DATOSXPAGINA);

			return [ 'usuarios' => $usuarios ];
		}

		public function upload()
		{
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();
			if (!$this->validateController($body->usuario)) {
				return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			try {
					$file = Request::file('file');
					$vc_imagen = Carbon::now()->timestamp .'_'. $file->getClientOriginalName();
					$file->move( public_path('images/usuarios'), $vc_imagen);

					return [ 'estatus'   => true, 'nombre' => $vc_imagen, 'url' => 'images/usuarios/' ];
			}
			catch (Exception $e) {
				return Response::json(['estatus'=> false, 'message' => $e->getMessage() ],418);
			}
		}

		public function eliminarImagen($vcImagen){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();
			if (!$this->validateController($body->usuario)) {
				return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			$usuario_imagen = '';
			$folder     		= 'images/usuarios/';

			try {
					$usuario_imagen = public_path( $folder . $vcImagen);
					unlink($usuario_imagen);

					return [ 'texto'  => 'Se ha eliminado la imagen correctamente.' ];
			}
			catch (Exception $e) {
				return Response::json([ 'texto' => $e->getMessage() ],418);
			}
		}
}
