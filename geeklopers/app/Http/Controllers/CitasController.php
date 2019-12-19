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
use App\Models\Citas;

class CitasController extends Controller
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

      return Citas::Filtro()->with('agenda')->paginate(self::DATOSXPAGINA);
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

    return Agendas::Filtro()->get();
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
        'id_agenda'      		=> 'required',
        'vc_nombre'      		=> 'required',
        'dt_fecha'      		=> 'required'
    ]);

    if ($validator->fails()) {
        return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
    }

    try
    {
        DB::beginTransaction();

        $dt_fecha = new Carbon($body->dt_fecha);
				$dt_fecha = $dt_fecha->subHours(7);
        // Validar si existe esta cita en cierta agenda
        if( Citas::Filtro()->where('id_agenda', $body->id_agenda)->where('vc_nombre', $body->vc_nombre)->exists() ){
          throw new Exception('Esta cita '.$body->vc_nombre.' ya se encuentra registrada en esta agenda.', 418);
        }
        // Validar si esta hora esta disponible
        if ( Citas::Filtro()->where('id_agenda', $body->id_agenda)->where('dt_fecha', $dt_fecha->toDateTimeString() )->exists() ) {
          throw new Exception('Esta fecha no esta disponible', 418);
        }

        Citas::create([
          'id_agenda'   => $body->id_agenda,
          'vc_nombre'   => $body->vc_nombre,
          'dt_fecha'    => $dt_fecha->toDateTimeString(),
          'id_creador'  => $body->usuario->id,
        ]);

        DB::commit();
        return ['texto' => 'La cita '. $body->vc_nombre .' a las '.$dt_fecha->toTimeString().' del dia '.$dt_fecha->toDateString().' fue guardada correctamente.'];
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

    $cita = Citas::Filtro()->with('agenda')->findOrFail($id);
    $cita->dt_fecha = new Carbon($cita->dt_fecha);

    return $cita;
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
        'id_agenda'      		=> 'required',
        'vc_nombre'      		=> 'required',
        'dt_fecha'      		=> 'required'
    ]);

    if ($validator->fails()) {
        return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
    }

    try
    {
        DB::beginTransaction();

        $dt_fecha = new Carbon($body->dt_fecha);
				$dt_fecha = $dt_fecha->subHours(7);
        // Validar si existe esta cita en cierta agenda
        if( Citas::Filtro()->where('id', '!=', $id)->where('id_agenda', $body->id_agenda)->where('vc_nombre', $body->vc_nombre)->exists() ){
          throw new Exception('Esta cita '.$body->vc_nombre.' ya se encuentra registrada en esta agenda.', 418);
        }
        // Validar si esta hora esta disponible
        if ( Citas::Filtro()->where('id', '!=', $id)->where('id_agenda', $body->id_agenda)->where('dt_fecha', $dt_fecha->toDateTimeString() )->exists() ) {
          throw new Exception('Esta fecha no esta disponible', 418);
        }

        $cita = Citas::Filtro()->with('agenda')->findOrFail($id);
        $cita->vc_nombre  = $body->vc_nombre;
        $cita->dt_fecha   = $dt_fecha->toDateTimeString();
        $cita->save();

        DB::commit();
        return ['texto' => 'La cita '. $body->vc_nombre .' a las '.$dt_fecha->toTimeString().' del dia '.$dt_fecha->toDateString().' fue actualizada correctamente.'];
    }
    catch (Exception $e)
    {
        DB::rollBack();
        return Response::json(['texto' => $e->getMessage()], 418);
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

        $cita = Citas::Filtro()->findOrFail($id);
        $cita->sn_activo    = self::INACTIVO;
        $cita->sn_eliminado = self::ACTIVO;
        $cita->save();
        $cita->delete();

        $cita->dt_fecha     = new Carbon($cita->dt_fecha);

				DB::commit();
        return ['texto' => 'La cita '. $cita->vc_nombre .' a las '.$cita->dt_fecha->toTimeString().' del dia '.$cita->dt_fecha->toDateString().' fue eliminada correctamente.'];
			}
			catch (Exception $e)
			{
				DB::rollBack();
				return Response::json(['texto' => 'La cita, no se pudo eliminar correctamente.'], 418);
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

      return Citas::Filtro()->with('agenda')
                            ->where(function( $q ) use( $nombre ){
                              $q->where('vc_nombre', 'like', '%'.$nombre.'%');
                            })
                            ->paginate(self::DATOSXPAGINA);
		}
}
