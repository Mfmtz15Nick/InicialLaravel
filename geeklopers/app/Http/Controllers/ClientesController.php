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
use Carbon\Carbon;

class ClientesController extends Controller
{
    const ADMINISTRADOR = 2;
    const AUXILIAR      = 3;
    const CONSULTOR     = 4;
    const ACTIVO        = 1;
    const INACTIVO      = 0;

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
