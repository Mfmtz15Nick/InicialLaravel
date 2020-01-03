<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Auth;
use Hash;
use Session;
use Response;
use Exception;
use Validator;
use Request;
use App\Models\Clientes;
use App\Models\ClientesDetalles;
use App\Models\ProyectosDetalles;
use App\Models\TiposEventosDetalles;
use App\Models\EventosDetalles;
use App\Models\ClientesEventos;
use Carbon\Carbon;

class ClientesController extends Controller
{
    const ADMINISTRADOR = 2;
    const AUXILIAR      = 3;
    const CONSULTOR     = 4;
    const ACTIVO        = 1;
    const INACTIVO      = 0;
	const DIAS = [ 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo' ];
	const MESES = [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ];
    

    public function validateController($usuario)
    {
        // Validar parametros de session necesarios
        if (!isset($usuario->rol->id)) {
            return false;
        }

        // Validar acceso permitido por Roles
        $rol = $usuario->rol->id_rol;
        if ( $rol != self::ADMINISTRADOR && $rol != self::AUXILIAR && $rol != self::CONSULTOR ) {
            return false;
        }

        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return  Response
     */
    public function index()
    {
        $body = (object) Request::all();

        // Verificación para el uso del Controllador
        if (!$this->validateController($body->usuario)) {
            return Response::json(['texto' => "Actualmente no cuenta con permisos"], 418);
        }

        return Clientes::Filtro()->with('detalle')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return  Response
     */
    public function store()
    {
        $body = (object) Request::all();

        // Verificación para el uso del Controllador
        if (!$this->validateController($body->usuario)) {
            return Response::json(['texto' => "Actualmente no cuenta con permisos"], 418);
        }
        // Validacion de parametros
        $validator = Validator::make((array) $body, [
            'vc_nombre'              => 'required',
            'vc_apellido'            => 'required',
            'nu_celular'             => 'required',
            
        ]);

        if ($validator->fails()) {
            return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
        }


        try {
            DB::beginTransaction();

            if (ClientesDetalles::Filtro()->where('vc_nombre', $body->vc_nombre)->exists()) {
                throw new Exception("El Cliente " . $body->vc_nombre . " ya se encuentra registrado.", 418);
            }
            $cliente = Clientes::create([]);

            $clienteDetalle = ClientesDetalles::create([
                'id_cliente'    => $cliente->id,
                'vc_nombre'     => $body->vc_nombre,
                'vc_apellido'   => $body->vc_apellido,
                'nu_celular'    => $body->nu_celular,
                'id_creador'    => $body->usuario->id,
            ]);


            DB::commit();
            return ['texto' => 'El Cliente ' . $body->vc_nombre . ', fue guardado. DAME->' . $cliente->id];
        } catch (Exception $e) {
            DB::rollBack();
            return Response::json(['texto' => $e->getMessage()], 418);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return  Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return  Response
     */
    public function edit($id)
    {
        $body = (object) Request::all();

        if (!$this->validateController($body->usuario)) {
            return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
        }
        return Clientes::Filtro()->with('detalle')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param   Request  $request
     * @param  int  $id
     * @return  Response
     */
    public function update($id)
    {
        $body = (object) Request::all();

        // Verificación para el uso del Controllador
        if (!$this->validateController($body->usuario)) {
            return Response::json(['texto' => "Actualmente no cuenta con permisos"], 418);
        }
        // Validacion de parametros
        $validator = Validator::make((array) $body, [
            'vc_nombre'               => 'required',
            'vc_apellido'             => 'required',
            'nu_celular'              => 'required',
        ]);
        if ($validator->fails()) {
            return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
        }

        try {
            DB::beginTransaction();
            if ( ClientesDetalles::Filtro()->where('id', '!=', $id)->where('vc_nombre', $body->vc_nombre)->exists() ) {
				throw new Exception('La promoción '.$body->vc_nombre.' ya esta registrada.', 418);
			  }
			  $cliente = ClientesDetalles::Filtro()->findOrFail($id);
 
			  $cliente->vc_nombre              = $body->vc_nombre;
			  $cliente->vc_apellido         = $body->vc_apellido;
			  $cliente->nu_celular           = $body->nu_celular;
			  $cliente->save();
			
            DB::commit();
            return ['texto' => 'El cliente ' . $body->vc_nombre . ', fue actualizado.'];
        } catch (Exception $e) {
            DB::rollBack();
            return Response::json(['texto' => $e->getMessage(), 'linea' => $e->getLine()], 418);
        }
    }

    // METODOS PARA EVENTOS DE LOS CLIENTES

		public function eventos($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
			     return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}
            //agendas horarios
            //clientes eventos
			// $cliente =  Clientes::Filtro()->with('eventos','detalle','eventos.eventoDetalle')->findOrFail($id);
			$cliente =  Clientes::Filtro()->with('eventos','detalle','eventos.nombreEvento')->findOrFail($id);

			$eventos = array();

	    return ['cliente' => $cliente, 'eventos' => $eventos];

        }
        public function eventosNuevo($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}
            $clientes = Clientes::Filtro()->with('eventos')->findOrFail($id);
            $tiposEventos = EventosDetalles::Filtro()->get();
            // return $clientes;
            return [ 'clientes' => $clientes, 'tiposEventos' => $tiposEventos ];
        }
        
        public function eventosStore($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			// Validacion de parametros
			$validator = Validator::make((array)$body, [
					'nu_dia'      		=> 'required',
                    'nu_mes'      		=> 'required',
					'id_evento'    		=> 'required',
					'tm_entrada'     	=> 'required',
					'tm_salida'      	=> 'required'
			]);

			if ($validator->fails()) {
					return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
			}

			try {
				DB::beginTransaction();

				$cliente = Clientes::Filtro()->findOrFail($id);
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

				if ( ClientesEventos::Filtro()
																	->where('id_cliente', $id)
																	->where('nu_dia', $body->nu_dia)
																	->where('tm_entrada', $tm_entrada->toTimeString() )
																	->where('tm_salida', $tm_salida->toTimeString() )
																	->exists() ) {
					throw new Exception('Este horario en la agenda '.$cliente->vc_nombre.' ya esta registrado', 418);
				}

				

                // Se crea el registro de la hora de la agenda
                
				ClientesEventos::create([
					'id_cliente' 	=> $id,
					'id_evento' 	=> $body->id_evento,
					'nu_dia' 		=> $body->nu_dia,
					'nu_mes' 		=> $body->nu_mes,
					'tm_entrada' 	=> $tm_entrada->toTimeString(),
					'tm_salida' 	=> $tm_salida->toTimeString(),
					'id_creador' 	=> $body->usuario->id
				]);

				DB::commit();
				return ['texto' => 'El horario de la agenda '. $cliente->vc_nombre .', fue creado correctamente.'];
			} catch (Exception $e) {
				DB::rollBack();
				return Response::json(['texto' => $e->getMessage()], 418);
			}

        }
        
		public function eventoGetById($id){
			// Verificación para el uso del Controllador
			$body = (Object)Request::all();

			if (!$this->validateController($body->usuario)) {
					 return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
			}

			$clientesEventos = ClientesEventos::Filtro()->with('cliente')->findOrFail($id);
			$clientesEventos->tm_entrada 	=  new Carbon($clientesEventos->tm_entrada);
			$clientesEventos->tm_salida 	=  new Carbon($clientesEventos->tm_salida);

			return $clientesEventos;
		}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return  Response
     */
    public function destroy($id)
    {
        $body = (object) Request::all();

        // Verificación para el uso del Controllador
        if (!$this->validateController($body->usuario)) {
            return Response::json(['texto' => "Actualmente no cuenta con permisos"], 418);
        }

        try {
            DB::beginTransaction();

            
            $cliente  = Clientes::Filtro()->findOrFail($id);
            $cliente->sn_activo          = self::INACTIVO;
            $cliente->sn_eliminado       = self::ACTIVO;
            $cliente->save();
            $cliente->delete();

            $clienteDetalle  = ClientesDetalles::Filtro()->where('id_cliente', $id)->first();
            $clienteDetalle->sn_activo          = self::INACTIVO;
            $clienteDetalle->sn_eliminado       = self::ACTIVO;
            $clienteDetalle->save();
            $clienteDetalle->delete();

            DB::commit();
            return ['texto' => 'El cliente ' . $clienteDetalle->vc_nombre . ', fue eliminado.'];
        } catch (Exception $e) {
            DB::rollBack();
            return Response::json(['texto' => $e->getMessage()], 418);
        }
    }

}
