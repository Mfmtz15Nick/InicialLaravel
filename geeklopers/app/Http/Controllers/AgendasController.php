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

	const DIAS = [ 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo' ];
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


		public function horarios($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
			     return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			$agenda =  Agendas::Filtro()->with('horarios')->findOrFail($id);

			$horarios = array();

	    for ( $i = 0; $i < count(self::DIAS); $i++ ) {

	      $horariosDias = array();

	      for ( $j = 0; $j < count($agenda->horarios); $j++) {
	          if ($agenda->horarios[$j]->nu_dia == ( $i + 1) ) {
	            array_push( $horariosDias, $agenda->horarios[$j] );
	          }
	      }

	      $dia = [
	        'id'        => ($i + 1),
	        'vc_nombre' => self::DIAS[$i],
	        'horarios'  => $horariosDias
	      ];

	      array_push( $horarios, $dia );
	    }

	    return ['agenda' => $agenda, 'horarios' => $horarios];

		}

		public function horariosNuevo($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			return Agendas::Filtro()->with('horarios')->findOrFail($id);
		}

		public function horariosStore($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			// Validacion de parametros
			$validator = Validator::make((array)$body, [
					'nu_dia'      		=> 'required',
					'tm_entrada'     	=> 'required',
					'tm_salida'      	=> 'required'
			]);

			if ($validator->fails()) {
					return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
			}

			try {
				DB::beginTransaction();

				$agenda = Agendas::Filtro()->findOrFail($id);
				// Se crea las horas como Carbon
			 	$tm_entrada = new Carbon($body->tm_entrada);
				$tm_salida 	= new Carbon($body->tm_salida);
				// Se restan 7 horas por la zona horaria
				$tm_entrada = $tm_entrada->subHours(7);
				$tm_salida  = $tm_salida->subHours(7);
				// Se valida si la hora de entrada es menor a la hora de salida
				if ( $tm_entrada->greaterThanOrEqualTo($tm_salida) ) {
					throw new Exception('La hora de entrada debe ser menor a la hora de salida.', 418);
				}

				if ( AgendasHorarios::Filtro()
																	->where('id_agenda', $id)
																	->where('nu_dia', $body->nu_dia)
																	->where('tm_entrada', $tm_entrada->toTimeString() )
																	->where('tm_salida', $tm_salida->toTimeString() )
																	->exists() ) {
					throw new Exception('Este horario en la agenda '.$agenda->vc_nombre.' ya esta registrado', 418);
				}

				if(
						// D
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 8:00 - 11:00
						// 8:00 > 9:00 - 10:00 < 11:00
						// tm_entrada > 9:00 - 10:00 < tm_salida
						AgendasHorarios::Filtro()
																			->where('id_agenda', $id)
																			->where('nu_dia', $body->nu_dia)
																			->where('tm_entrada', '>', $tm_entrada->toTimeString() )
																			->where('tm_salida', '<', $tm_salida->toTimeString() )
																			->exists()
						||
						// A
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 8:00 - 9:30
						// 8:00 > 9:00 - > 9:30 < 10:00
						// tm_entrada > 9:00  && 9:00 < tm_salida && tm_salida > 10:00
						AgendasHorarios::Filtro()
																			->where('id_agenda', $id)
																			->where('nu_dia', $body->nu_dia)
																			// 9:00 > 9:30
																			->where('tm_entrada', '>', $tm_entrada->toTimeString() )
																			// 9:00 < 11:00
																			->where('tm_entrada', '<', $tm_salida->toTimeString() )
																			// 10:00 > 11:00
																			->where('tm_salida', '>', $tm_salida->toTimeString() )
																			->exists()
						||
						// B
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 9:30 - 10:30
						// 9:00 < 9:30 > 10:00 < 10:30
						// 9:00 < tm_entrada && 10:00 > tm_entrada && 10:00 < tm_salida
						AgendasHorarios::Filtro()
																			->where('id_agenda', $id)
																			->where('nu_dia', $body->nu_dia)
																			// 9:00 < 9:30
																			->where('tm_entrada', '<', $tm_entrada->toTimeString() )
																			// 10:00 > 9:30
																			->where('tm_salida', '>', $tm_entrada->toTimeString() )
																			// 10:00 < 11:00
																			->where('tm_salida', '<', $tm_salida->toTimeString() )
																			->exists()
						||
						// C
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 9:15 - 9:45
						// 9:00 < 9:15 - 9:45 < 10:00
						// 9:00 < tm_entrada && 10:00 > tm_entrada && 10:00 < tm_salida
						AgendasHorarios::Filtro()
																			->where('id_agenda', $id)
																			->where('nu_dia', $body->nu_dia)
																			// 9:00 < 9:15
																			->where('tm_entrada', '<', $tm_entrada->toTimeString() )
																			// 10:00 > 9:15
																			->where('tm_salida', '>', $tm_entrada->toTimeString() )
																			// 9:00 < 9:45
																			->where('tm_entrada', '<', $tm_salida->toTimeString() )
																			// 10:00 > 9:45
																			->where('tm_salida', '>', $tm_salida->toTimeString() )
																			->exists()
					||

					// E
					// Fecha registrada: 9:00 - 10:00
					// Fecha a agregar: 9:30 - 10:00
					// 9:00 < 9:30 - 10:00 <= 10:00
					// 9:00 < tm_entrada && 10:00 > tm_entrada && 10:00 <= tm_salida
					AgendasHorarios::Filtro()
																		->where('id_agenda', $id)
																		->where('nu_dia', $body->nu_dia)
																		// 9:00 < 9:15
																		->where('tm_entrada', '<', $tm_entrada->toTimeString() )
																		// 10:00 > 9:15
																		->where('tm_salida', '>=', $tm_salida->toTimeString() )
																		->exists()
					||
					// F
					// Fecha registrada: 9:00 - 10:00
					// Fecha a agregar: 9:00 - 9:30
					// 9:00 <= 9:00 - 9:30 < 10:00
					// 9:00 <= tm_entrada && 10:00 > tm_entrada && 10:00 < tm_salida
					AgendasHorarios::Filtro()
																		->where('id_agenda', $id)
																		->where('nu_dia', $body->nu_dia)
																		// 9:00 < 9:15
																		->where('tm_entrada', '<=', $tm_entrada->toTimeString() )
																		// 10:00 > 9:15
																		->where('tm_salida', '>', $tm_salida->toTimeString() )
																		->exists()

					||
					AgendasHorarios::Filtro()
										->where([
												['id_agenda', $id],
												['nu_dia', $body->nu_dia]
										])
										->whereBetween('tm_entrada', [ $tm_entrada->toTimeString(), $tm_salida->toTimeString()])
										->whereBetween('tm_salida', [ $tm_entrada->toTimeString(), $tm_salida->toTimeString()])
										->exists()

				){
					throw new Exception('Este horario choca con una hora registrada.', 418);
				}

				// Se crea el registro de la hora de la agenda
				AgendasHorarios::create([
					'id_agenda' 	=> $id,
					'nu_dia' 			=> $body->nu_dia,
					'tm_entrada' 	=> $tm_entrada->toTimeString(),
					'tm_salida' 	=> $tm_salida->toTimeString(),
					'id_creador' 	=> $body->usuario->id
				]);

				DB::commit();
				return ['texto' => 'El horario de la agenda '. $agenda->vc_nombre .', fue creado correctamente.'];
			} catch (Exception $e) {
				DB::rollBack();
				return Response::json(['texto' => $e->getMessage()], 418);
			}

		}



		public function horarioGetById($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			$agendaHorario = AgendasHorarios::Filtro()->with('agenda')->findOrFail($id);
			$agendaHorario->tm_entrada 	=  new Carbon($agendaHorario->tm_entrada);
			$agendaHorario->tm_salida 	=  new Carbon($agendaHorario->tm_salida);

			return $agendaHorario;
		}

		public function horarioUpdate($id){

			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			// Validacion de parametros
			$validator = Validator::make((array)$body, [
					'nu_dia'      		=> 'required',
					'tm_entrada'     	=> 'required',
					'tm_salida'      	=> 'required'
			]);

			if ($validator->fails()) {
					return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
			}

			try {
				DB::beginTransaction();

				$agendaHorario = AgendasHorarios::Filtro()->with('agenda')->findOrFail($id);

				// Se crea las horas como Carbon
			 	$tm_entrada = new Carbon($body->tm_entrada);
				$tm_salida 	= new Carbon($body->tm_salida);
				// Se restan 7 horas por la zona horaria
				$tm_entrada = $tm_entrada->subHours(7);
				$tm_salida  = $tm_salida->subHours(7);
				// Se valida si la hora de entrada es menor a la hora de salida
				if ( $tm_entrada->greaterThanOrEqualTo($tm_salida) ) {
					throw new Exception('La hora de entrada debe ser menor a la hora de salida.', 418);
				}

				if ( AgendasHorarios::Filtro()
																	->where('id', '!=', $id)
																	->where('id_agenda', $agendaHorario->id_agenda)
																	->where('nu_dia', $body->nu_dia)
																	->where('tm_entrada', $tm_entrada->toTimeString() )
																	->where('tm_salida', $tm_salida->toTimeString() )
																	->exists() ) {
					throw new Exception('Este horario en la agenda '.$agendaHorario->agenda->vc_nombre.' ya esta registrado', 418);
				}

				if(
						// D
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 8:00 - 11:00
						// 8:00 > 9:00 - 10:00 < 11:00
						// tm_entrada > 9:00 - 10:00 < tm_salida
						AgendasHorarios::Filtro()
																			->where('id', '!=', $id)
																			->where('id_agenda', $agendaHorario->id_agenda)
																			->where('nu_dia', $body->nu_dia)
																			->where('tm_entrada', '>', $tm_entrada->toTimeString() )
																			->where('tm_salida', '<', $tm_salida->toTimeString() )
																			->exists()
						||
						// A
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 8:00 - 9:30
						// 8:00 > 9:00 - > 9:30 < 10:00
						// tm_entrada > 9:00  && 9:00 < tm_salida && tm_salida > 10:00
						AgendasHorarios::Filtro()
																			->where('id', '!=', $id)
																			->where('id_agenda', $agendaHorario->id_agenda)
																			->where('nu_dia', $body->nu_dia)
																			// 9:00 > 9:30
																			->where('tm_entrada', '>', $tm_entrada->toTimeString() )
																			// 9:00 < 11:00
																			->where('tm_entrada', '<', $tm_salida->toTimeString() )
																			// 10:00 > 11:00
																			->where('tm_salida', '>', $tm_salida->toTimeString() )
																			->exists()
						||
						// B
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 9:30 - 10:30
						// 9:00 < 9:30 > 10:00 < 10:30
						// 9:00 < tm_entrada && 10:00 > tm_entrada && 10:00 < tm_salida
						AgendasHorarios::Filtro()
																			->where('id', '!=', $id)
																			->where('id_agenda', $agendaHorario->id_agenda)
																			->where('nu_dia', $body->nu_dia)
																			// 9:00 < 9:30
																			->where('tm_entrada', '<', $tm_entrada->toTimeString() )
																			// 10:00 > 9:30
																			->where('tm_salida', '>', $tm_entrada->toTimeString() )
																			// 10:00 < 11:00
																			->where('tm_salida', '<', $tm_salida->toTimeString() )
																			->exists()
						||
						// C
						// Fecha registrada: 9:00 - 10:00
						// Fecha a agregar: 9:15 - 9:45
						// 9:00 < 9:15 - 9:45 < 10:00
						// 9:00 < tm_entrada && 10:00 > tm_entrada && 10:00 < tm_salida
						AgendasHorarios::Filtro()
																			->where('id', '!=', $id)
																			->where('id_agenda', $agendaHorario->id_agenda)
																			->where('nu_dia', $body->nu_dia)
																			// 9:00 < 9:15
																			->where('tm_entrada', '<', $tm_entrada->toTimeString() )
																			// 10:00 > 9:15
																			->where('tm_salida', '>', $tm_entrada->toTimeString() )
																			// 9:00 < 9:45
																			->where('tm_entrada', '<', $tm_salida->toTimeString() )
																			// 10:00 > 9:45
																			->where('tm_salida', '>', $tm_salida->toTimeString() )
																			->exists()


					||
					// E
					// Fecha registrada: 9:00 - 10:00
					// Fecha a agregar: 9:30 - 10:00
					// 9:00 < 9:30 - 10:00 <= 10:00
					// 9:00 < tm_entrada && 10:00 > tm_entrada && 10:00 <= tm_salida
					AgendasHorarios::Filtro()
																		->where('id', '!=', $id)
																		->where('id_agenda', $agendaHorario->id_agenda)
																		->where('nu_dia', $body->nu_dia)
																		// 9:00 < 9:15
																		->where('tm_entrada', '<', $tm_entrada->toTimeString() )
																		// 10:00 > 9:15
																		->where('tm_salida', '>=', $tm_salida->toTimeString() )
																		->exists()
					||
					// F
					// Fecha registrada: 9:00 - 10:00
					// Fecha a agregar: 9:00 - 9:30
					// 9:00 <= 9:00 - 9:30 < 10:00
					// 9:00 <= tm_entrada && 10:00 > tm_entrada && 10:00 < tm_salida
					AgendasHorarios::Filtro()
																		->where('id', '!=', $id)
																		->where('id_agenda', $agendaHorario->id_agenda)
																		->where('nu_dia', $body->nu_dia)
																		// 9:00 < 9:15
																		->where('tm_entrada', '<=', $tm_entrada->toTimeString() )
																		// 10:00 > 9:15
																		->where('tm_salida', '>', $tm_salida->toTimeString() )
																		->exists()

					||
					AgendasHorarios::Filtro()
										->where([
												['id', '!=', $id],
												['id_agenda', $agendaHorario->id_agenda],
												['nu_dia', $body->nu_dia]
										])
										->whereBetween('tm_entrada', [ $tm_entrada->toTimeString(), $tm_salida->toTimeString()])
										->whereBetween('tm_salida', [ $tm_entrada->toTimeString(), $tm_salida->toTimeString()])
										->exists()

				){
					throw new Exception('Este horario choca con una hora registrada.', 418);
				}

				$agendaHorario->tm_entrada 	= $tm_entrada->toTimeString();
				$agendaHorario->tm_salida 	= $tm_salida->toTimeString();
				$agendaHorario->save();

				DB::commit();
				return ['texto' => 'El horario de la agenda '. $agendaHorario->agenda->vc_nombre .', fue actualizado correctamente.'];
			} catch (Exception $e) {
				DB::rollBack();
				return Response::json(['texto' => $e->getMessage()], 418);
			}
		}

		public function horarioDelete($id){

			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			try {
				DB::beginTransaction();

				$agendaHorario = AgendasHorarios::Filtro()->with('agenda')->findOrFail($id);
				$agendaHorario->sn_activo 		= self::INACTIVO;
				$agendaHorario->sn_eliminado 	= self::ACTIVO;
				$agendaHorario->save();
				$agendaHorario->delete();

				DB::commit();
				return ['texto' => 'El horario de la agenda '. $agendaHorario->agenda->vc_nombre .', fue eliminado correctamente.'];
			} catch (Exception $e) {
				DB::rollBack();
				Log::info(['texto' => $e->getMessage()]);
				return Response::json(['texto' => $e->getMessage()], 418);
			}

		}
}
