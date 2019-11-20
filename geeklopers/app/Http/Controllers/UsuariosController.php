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
	const DATOSXPAGINA 		= 2;
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
			return Usuarios::Filtro()->with('rol.rol', 'detalle')->paginate(self::DATOSXPAGINA);
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
            'vc_password_re'    => 'required'
        ]);

        if ($validator->fails()) {
            return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
        }

        // Validar contraseñas
        if ($body->vc_password != $body->vc_password_re) {
            return Response::json(['texto' => 'Las contraseñas proporcionadas no son identicas.'], 418);
		}

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
            	UsuariosDetalles::create([
            		'id_usuario' 	=> $usuario->id,
            		'id_genero' 	=> $body->id_genero,
            		'vc_nombre' 	=> $body->vc_nombre,
            		'vc_apellido' => $body->vc_apellido,
            		'vc_email' 		=> $body->vc_email,
            		'vc_password' => $body->vc_password,
            		'id_creador' 	=> $body->usuario->id
            	]);
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
			'vc_password' => $usuario->detalle->vc_password
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

        try
        {
            DB::beginTransaction();

            //Verifica si existe el correo en la BD
            if( UsuariosDetalles::Filtro()->EsEmail($body->vc_email)->where('id_usuario', '!=', $id)->exists() ){
                throw new Exception('El correo '. $body->vc_email .', ya se encuentra registrado.', 418);
            } else {
								// Obtener el detalle del Usuario
						    $usuarioDetalle = UsuariosDetalles::Filtro()->where('id_usuario', $id)->first();
						    $usuarioDetalle->sn_activo 		= self::INACTIVO;
						    $usuarioDetalle->sn_eliminado = self::ACTIVO;
						    $usuarioDetalle->save();
					      $usuarioDetalle->delete();

	            	// Crear nuevo detalle del Usuario
	            	UsuariosDetalles::create([
	            		'id_usuario' 			=> $id,
	            		'id_genero' 			=> $body->id_genero,
	            		'vc_nombre' 			=> $body->vc_nombre,
	            		'vc_apellido' 		=> $body->vc_apellido,
	            		'vc_email' 				=> $body->vc_email,
	            		'vc_password' 		=> $body->vc_password,
	            		'id_creador'			=> $body->usuario->id
								]);
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
			public function buscar($nombre)
			{
				// Verificación para el uso del Controllador
				$body = (Object)Request::all();

				if (!$this->validateController($body->usuario)) {
				return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
				}

				// Obtener usuarios del sistema
				return Usuarios::Filtro()->with('rol.rol', 'detalle')->paginate(self::DATOSXPAGINA);
			}

}
