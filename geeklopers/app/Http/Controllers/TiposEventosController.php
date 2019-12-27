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
use App\Models\TiposEventos;
use App\Models\TiposEventosDetalles;
use App\Models\ProyectosDetalles;
use Carbon\Carbon;

class TiposEventosController extends Controller
{
    const ADMINISTRADOR = 2;
    const ACTIVO        = 1;
    const INACTIVO      = 0;

    public function validateController($usuario)
    {
        // Validar parametros de session necesarios
        if (!isset($usuario->rol->id)) {
            return false;
        }

        // Validar acceso permitido por Roles
        if ($usuario->rol->id_rol != self::ADMINISTRADOR) {
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

        return TiposEventos::Filtro()->with('detalle')->get();
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
            'vc_imagenUrl'           => 'required',
        ]);

        if ($validator->fails()) {
            return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
        }

        $tmp_imagen 	  = [];
        $ext 			      = [];
        $tmp_imagenEng 	= [];
        $extEng 		    = [];
        $folder     	  = 'images/clientes/';

        try {
            DB::beginTransaction();

            if (TiposEventosDetalles::Filtro()->where('vc_nombre', $body->vc_nombre)->exists()) {
                throw new Exception("El Cliente " . $body->vc_nombre . " ya se encuentra registrado.", 418);
            }
            $tipoEvento = TiposEventos::create([]);

            $tipoEventoDetalle = TiposEventosDetalles::create([
                'id_tiposEventos'    => $tipoEvento->id,
                'vc_nombre'     => $body->vc_nombre,
                'vc_imagen'     => $body->vc_imagenUrl,
                'vc_imagenUrl'  => $body->vc_imagenUrl,
                'id_creador'    => $body->usuario->id,
            ]);

            // --------------- GUARDAR LA IMAGEN ---------------
            // Primera Imagen
            $tmp_imagen = public_path( $folder . $body->vc_imagenUrl);

            $ext = pathinfo( $tmp_imagen,PATHINFO_EXTENSION);
            $vc_imagen = 'Imagen_'. $tipoEvento->id .'_'. Carbon::now()->timestamp .'.'. $ext;

            copy( $tmp_imagen, public_path( $folder . $vc_imagen ) );
            unlink($tmp_imagen);

            $tipoEventoDetalle->vc_imagen    = $vc_imagen;
            $tipoEventoDetalle->vc_imagenUrl = $vc_imagen;
            $tipoEventoDetalle->save();

            // -------------------------------------------------

            DB::commit();
            return ['texto' => 'El Cliente ' . $body->vc_nombre . ', fue guardado.'];
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
        return TiposEventos::Filtro()->with('detalle')->findOrFail($id);
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
            'vc_imagenUrl'            => 'required',
        ]);
        if ($validator->fails()) {
            return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
        }

        $tmp_imagen 	  = [];
        $ext 			      = [];
        $tmp_imagenEng 	= [];
        $extEng 		    = [];
        $folder     	  = 'images/clientes/';

        try {
            DB::beginTransaction();

            if (TiposEventosDetalles::Filtro()->where('id_tiposEventos', '!=', $id)->where('vc_nombre', $body->vc_nombre)->exists()) {
                throw new Exception("El Cliente " . $body->vc_nombre . " ya se encuentra registrado.", 418);
            }
            $tipoEvento                = TiposEventosDetalles::Filtro()->where('id_tiposEventos', $id)->first();
            $tipoEvento->vc_nombre     = $body->vc_nombre;
            $tipoEvento->vc_imagen     = $body->vc_imagenUrl;
            $tipoEvento->vc_imagenUrl  = $body->vc_imagenUrl;
            $tipoEvento->save();

            // --------------- GUARDAR LA IMAGEN ---------------
            // Primera Imagen
            $tmp_imagen = public_path( $folder . $body->vc_imagenUrl);

            $ext = pathinfo( $tmp_imagen,PATHINFO_EXTENSION);
            $vc_imagen = 'Imagen_'. $id .'_'. Carbon::now()->timestamp .'.'. $ext;

            copy( $tmp_imagen, public_path( $folder . $vc_imagen ) );
            unlink($tmp_imagen);

            $tipoEvento->vc_imagen    = $vc_imagen;
            $tipoEvento->vc_imagenUrl = $vc_imagen;
            $tipoEvento->save();
            // -------------------------------------------------

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

            if( ProyectosDetalles::Filtro()->where('id_cliente', $id)->exists() )
              throw new Exception("Existen proyectos asigandos a esta categoria.",418);

            $tipoEvento  = TiposEventos::Filtro()->findOrFail($id);
            $tipoEvento->sn_activo          = self::INACTIVO;
            $tipoEvento->sn_eliminado       = self::ACTIVO;
            $tipoEvento->save();
            $tipoEvento->delete();

            $tipoEventoDetalles  = TiposEventosDetalles::Filtro()->where('id_tiposEventos', $id)->first();
            $tipoEventoDetalles->sn_activo          = self::INACTIVO;
            $tipoEventoDetalles->sn_eliminado       = self::ACTIVO;
            $tipoEventoDetalles->save();
            $tipoEventoDetalles->delete();

            DB::commit();
            return ['texto' => 'El cliente ' . $tipoEventoDetalles->vc_nombre . ', fue eliminado.'];
        } catch (Exception $e) {
            DB::rollBack();
            return Response::json(['texto' => $e->getMessage()], 418);
        }
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
          $file->move( public_path('images/clientes'), $vc_imagen);

          return [ 'estatus'   => true, 'nombre' => $vc_imagen];
      }
      catch (Exception $e) {
        return Response::json(['estatus'=> false, 'message' => $e->getMessage() ],418);
      }
    }
}
