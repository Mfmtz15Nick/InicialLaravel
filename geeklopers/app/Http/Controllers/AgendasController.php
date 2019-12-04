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
use App\Models\Agendas;
use App\Models\AgendasHorarios;

class AgendasController extends Controller
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

      return Agendas::Filtro()->paginate(self::DATOSXPAGINA);
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
        'vc_nombre'      		=> 'required'
    ]);

    if ($validator->fails()) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
    }

    try
    {
        DB::beginTransaction();

        if( Agendas::Filtro()->where('vc_nombre', $body->vc_nombre)->exists() ){
          throw new Exception('Esta agenda '.$body->vc_nombre.' ya se encuentra registrada.', 418);
        }
        else {
          Agendas::create([
            'vc_nombre'   => $body->vc_nombre,
            'id_creador'  => $body->usuario->id,
          ]);
        }

        DB::commit();
        return ['texto' => 'La agenda '. $body->vc_nombre .', fue guardada correctamente.'];
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

    return Agendas::Filtro()->findOrFail($id);
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
        'vc_nombre'      		=> 'required'
    ]);

    if ($validator->fails()) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
    }

    try
    {
        DB::beginTransaction();

        if( Agendas::Filtro()->where('id', '!=', $id)->where('vc_nombre', $body->vc_nombre)->exists() ){
          throw new Exception('Esta agenda '.$body->vc_nombre.' ya se encuentra registrada.', 418);
        }
        else {
          $agenda = Agendas::Filtro()->findOrFail($id);
          $agenda->vc_nombre = $body->vc_nombre;
          $agenda->id_creador = $body->usuario->id;
          $agenda->save();
        }

        DB::commit();
        return ['texto' => 'La agenda '. $body->vc_nombre .', fue actualizada correctamente.'];
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

        $agenda = Agendas::Filtro()->findOrFail($id);
        $agenda->sn_activo = self::INACTIVO;
        $agenda->sn_eliminado = self::ACTIVO;
        $agenda->save();
        $agenda->delete();

				DB::commit();
				return ['texto' => 'La agenda '.$agenda->vc_nombre.' fue eliminada correctamente.'];
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
		public function buscarByNombre($nombre)
		{
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
			     return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

      return Agendas::Filtro()
                            ->where(function( $q ) use( $nombre ){
                              $q->where('vc_nombre', 'like', '%'.$nombre.'%');
                            })
                            ->paginate(self::DATOSXPAGINA);
		}
}
