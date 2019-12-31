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
use App\Models\Eventos;
use App\Models\EventosDetalles;
use App\Models\EventosImagenes;
use App\Models\TiposEventos;
use App\Models\TiposEventosDetalles;

use Carbon\Carbon;

class EventosController extends Controller
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

        return Eventos::Filtro()->with('imagen', 'imagenes','detalle.tiposEventos',)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return  Response
     */
    public function create()
    {
        $body = (object) Request::all();

        // Verificación para el uso del Controllador
        if (!$this->validateController($body->usuario)) {
            return Response::json(['texto' => "Actualmente no cuenta con permisos"], 418);
        }

        $tiposEventos = TiposEventosDetalles::Filtro()->get();
        
		    return [ 'tiposEventos' => $tiposEventos ];
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
            'id_tiposEventos'           => 'required',
            'vc_nombre'              		=> 'required',
        ]);
        if ($validator->fails()) {
            return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
        }

        

        try {
            DB::beginTransaction();

            if (EventosDetalles::Filtro()->where('vc_nombre', $body->vc_nombre)->where('id_tiposEventos', $body->id_tiposEventos)->exists()) {
                throw new Exception("El Proyecto " . $body->vc_nombre . " ya se encuentra registrado.", 418);
            }


            $evento        = Eventos::create([]);
            $eventoDetalle = EventosDetalles::create([
                'id_evento'       	=> $evento->id,
                'vc_nombre'         => $body->vc_nombre,
                'id_tiposEventos'   => $body->id_tiposEventos,
                // 'vc_imagen'         => $body->vc_imagenUrl,
                // 'vc_imagenUrl'      => $body->vc_imagenUrl,
                'id_creador'        => $body->usuario->id,
            ]);

            DB::commit();
            return ['texto' => 'El Proyecto ' . $body->vc_nombre . ', fue guardado.'];
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
        return Proyectos::Filtro()->with('detalle')->findOrFail($id);
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
          'id_categoria'           => 'required',
          'vc_nombre'              => 'required',
          'vc_nombreEng'           => 'required',
          'vc_descripcion'         => 'required',
          'vc_descripcionEng'      => 'required',
          'id_estado'              => 'required',
          'id_ciudad'              => 'required',
          'id_cliente'             => 'required',
          'vc_cubierta'            => 'required',
          'vc_cubiertaEng'         => 'required',
          'nu_peso'              	 => 'required',
          'id_medida'              => 'required'
          // 'vc_imagenUrl'           => 'required'
        ]);

        if ($validator->fails()) {
            return Response::json(['texto' => 'Asegurese de ingresar los datos necesarios.'], 418);
        }

        $tmp_imagen 	  = [];
        $ext 			      = [];
        $tmp_imagenEng 	= [];
        $extEng 		    = [];
        $folder     	  = 'images/proyectos/';


        try {
            DB::beginTransaction();

            if (ProyectosDetalles::Filtro()->where('id', '!=', $id)->where('vc_nombre', $body->vc_nombre)->where('id_categoria', $body->id_categoria)->exists()) {
                throw new Exception("El Proyecto " . $body->vc_nombre . " ya se encuentra registrado.", 418);
            }

            // $sn_destacado = 0;
            //
            // if ($body->sn_destacado){
            //   $sn_destacado = 1;
            //   if (ProyectosDetalles::Filtro()->where('id', '!=', $id)->where('sn_destacado', $sn_destacado)->where('id_categoria', $body->id_categoria)->exists()) {
            //       throw new Exception("Existe un proyecto destacado registrado en esta categoria.", 418);
            //   }
            // }

            $proyecto  = ProyectosDetalles::Filtro()->findOrFail($id);
            $proyecto->vc_nombre             = $body->vc_nombre;
      			$proyecto->vc_nombreEng          = $body->vc_nombreEng;
      			$proyecto->vc_descripcion        = $body->vc_descripcion;
      			$proyecto->vc_descripcionEng     = $body->vc_descripcionEng;
      			$proyecto->id_categoria          = $body->id_categoria;
            $proyecto->id_estado             = $body->id_estado;
      			$proyecto->id_ciudad             = $body->id_ciudad;
      			$proyecto->id_cliente            = $body->id_cliente;
      			$proyecto->vc_cubierta           = $body->vc_cubierta;
      			$proyecto->vc_cubiertaEng        = $body->vc_cubiertaEng;
      			$proyecto->nu_peso               = $body->nu_peso;
      			$proyecto->id_medida             = $body->id_medida;
            // $proyecto->vc_imagen             = $body->vc_imagenUrl;
            // $proyecto->vc_imagenUrl          = $body->vc_imagenUrl;
            $proyecto->save();

            // // --------------- GUARDAR LA IMAGEN ---------------
            // // Primera Imagen
            // $tmp_imagen = public_path( $folder . $body->vc_imagenUrl);
            //
            // $ext = pathinfo( $tmp_imagen,PATHINFO_EXTENSION);
            // $vc_imagen = 'Imagen_'. $id .'_'. Carbon::now()->timestamp .'.'. $ext;
            //
            // copy( $tmp_imagen, public_path( $folder . $vc_imagen ) );
            // unlink($tmp_imagen);
            //
            // $proyecto->vc_imagen    = $vc_imagen;
            // $proyecto->vc_imagenUrl = $vc_imagen;
            // $proyecto->save();
            // // -------------------------------------------------

            DB::commit();
            return ['texto' => 'El Proyecto ' . $body->vc_nombre . ', fue actualizado.'];
        } catch (Exception $e) {
            DB::rollBack();
            return Response::json(['texto' => $e->getMessage()], 418);
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

            $proyecto  = Proyectos::Filtro()->findOrFail($id);
            $proyecto->sn_activo          = self::INACTIVO;
            $proyecto->sn_eliminado       = self::ACTIVO;
            $proyecto->save();
            $proyecto->delete();

            $proyectoDetalle  = ProyectosDetalles::Filtro()->where('id_proyecto', $id)->first();
            $proyectoDetalle->sn_activo          = self::INACTIVO;
            $proyectoDetalle->sn_eliminado       = self::ACTIVO;
            $proyectoDetalle->save();
            $proyectoDetalle->delete();

            $proyectoImagenes  = ProyectosImagenes::Filtro()->where('id_proyecto', $id)->get();
            foreach ($proyectoImagenes as $imagenes) {
              $imagenes->sn_activo          = self::INACTIVO;
              $imagenes->sn_eliminado       = self::ACTIVO;
              $imagenes->save();
              $imagenes->delete();
            }

            DB::commit();
            return ['texto' => 'El Proyecto ' . $proyectoDetalle->vc_nombre . ', fue eliminado.'];
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
          $file->move( public_path('images/proyectos'), $vc_imagen);

          return [ 'estatus'   => true, 'nombre' => $vc_imagen];
      }
      catch (Exception $e) {
        return Response::json(['estatus'=> false, 'message' => $e->getMessage() ],418);
      }
    }

    //IMAGENES
    public function indexImagenes($id)
    {
      // Verificación para el uso del Controllador
      $body = (Object)Request::all();
      if (!$this->validateController($body->usuario)) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
      }

      return Proyectos::Filtro()->with('detalle','imagenes')->findOrFail($id);
    }

    public function storeImagenes($id)
    {
      // Verificación para el uso del Controllador
      $body = (Object)Request::all();

      if (!$this->validateController($body->usuario)) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
      }

      $tmp_imagen   = [];
      $ext 		      = [];

      try {
      DB::beginTransaction();

      //Guardamos la imagen
      if(count($body->proyecto_imagenes) > 0) {
        // Obtenemos la ultima posicion
        $ultimo   = ProyectosImagenes::where('id_proyecto', $id )->orderBy('nu_posicion', 'DESC')->first(['nu_posicion']);
        $nu_orden = is_null( $ultimo ) ? 1 : ( $ultimo->nu_posicion + 1  );
        $folder   = 'images/proyectos/';

        // Recorremos las imagenes
        foreach ($body->proyecto_imagenes as $vc_imagen_temp) {

          $imagen = ProyectosImagenes::create([
            'id_proyecto'   => $id,
            'vc_imagen'		  => '',
            'vc_imagenUrl'	=> '',
            'nu_posicion'	  => $nu_orden,
            'id_creador' 	  => $body->usuario->id
          ]);

          $tmp_imagen = public_path( $folder . $vc_imagen_temp);

          $ext = pathinfo( $tmp_imagen,PATHINFO_EXTENSION);
          $vc_imagen = 'Imagen_P_'.$id.'_'. $imagen->id .'_'. Carbon::now()->timestamp .'.'. $ext;

          $imagen->vc_imagen 	  = $vc_imagen;
          $imagen->vc_imagenUrl = $vc_imagen;
          $imagen->save();

          copy( $tmp_imagen, public_path( $folder . $vc_imagen ) );
          unlink($tmp_imagen);

          $nu_orden++;
        }
      }

        DB::commit();
        return ['texto' => 'Se han guardado las imagenes correctamente'];
      }
      catch(Exception $e){
        DB::rollBack();
        if( $e->getCode() == 999 ) return Response::json(['texto' => $e->getMessage(), 'line' => $e->getLine()],418);
        return Response::json(['message' => $e->getMessage(), 'line' => $e->getLine()],418);
      }
    }

    public function destroyImagenes($id)
    {
      // Verificación para el uso del Controllador
      $body = (Object)Request::all();
      if (!$this->validateController($body->usuario)) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
      }

      try {
        DB::beginTransaction();

        // Eliminar imagen
        $imagen = ProyectosImagenes::Filtro()->findOrFail($id);
        $imagen->sn_activo          = self::INACTIVO;
        $imagen->sn_eliminado       = self::ACTIVO;
        $imagen->save();
        $imagen->delete();

        DB::commit();
        return ['texto' => 'La imagen, fue eliminada correctamente.'];
        }
        catch (Exception $e){
        DB::rollBack();
        return Response::json(['texto' => 'La imagen, no se pudo eliminar correctamente. @USU3'], 418);
        }
    }

    public function ordenarImagenes()
    {
      // Verificación para el uso del Controllador
      $body = (Object)Request::all();
      if (!$this->validateController($body->usuario)) {
        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
      }

      try {
        DB::beginTransaction();

        foreach ($body->proyecto_imagenes as $item) {
          $item = (object)$item;
          $imagen = ProyectosImagenes::Filtro()->findOrFail( $item->id );
          $imagen->nu_posicion = $item->nu_orden;
          $imagen->save();
        }

        DB::commit();
        return Response::json(['texto' => 'Se ha actualizado el orden de las imágenes correctamente']);

      } catch (Exception $e) {
        DB::rollBack();
        return Response::json(['texto' => 'No se pudo realizar el ordenamiento de las imágenes correctamente.'], 418);
      }
    }

}
