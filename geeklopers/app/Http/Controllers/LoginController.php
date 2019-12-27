<?php namespace App\Http\Controllers;

use DB;
use Log;
use Auth;
use Hash;
use JWTAuth;
use Request;
use Session;
use Response;
use Exception;
use Validator;
use JWTFactory;

use Carbon\Carbon;
use App\Models\Usuarios;
use App\Models\UsuariosRoles;
use App\Models\UsuariosDetalles;
use App\Models\UsuariosTokens;

class LoginController extends Controller
{
    /**
	 * Metodo para Generar un Inicio de Sesión.
	 *
	 * @return Response
	 */
    public function login()
    {
        $body = (Object)Request::all();


        // Validacion de parametros
        $validator = Validator::make((array)$body, [
            'vc_email'       => 'required',
            'vc_password'    => 'required'

        ]);

        if ($validator->fails()) {
            return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
        }

        try
        {
            $usuarioDetalles = UsuariosDetalles::Filtro()->EsEmail($body->vc_email)->first();

            if( !$usuarioDetalles ) {
                throw new Exception('El usuario proporcionado actualmente no se encuenta registrado.');
            } else {

                // Obtener usuario con todos su roles
                $usuario = Usuarios::Filtro()->with('detalle', 'rol')->find($usuarioDetalles->id_usuario);

                if( $usuario->sn_activo == 1 ){

                    // Verificar los privilegios del rol
                    $rol = $usuario->rol->id_rol;
                    if ( $rol != 2 && $rol != 4) {
                        return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 418);
                    }

                    if( $usuario->detalle->vc_password == $body->vc_password ){

                        /*
                        *
                        *   Nueva estructura de login
                        *   por tokens
                        *
                        */

                        // Proceso para generar un token en base a los registros
                        $tm_carbon = Carbon::now();

                        $customClaims = [
                            'sub'   => $usuario->id,
                            'rol'   => $usuario->rol->id_rol,
                            'key'   => env('APP_KEY'),
                            'iat'   => $tm_carbon->timestamp,
                            'exp'   => $tm_carbon->addDays(env('TOKEN_EXPIRED_ADMIN'))->timestamp,
                        ];

                        $payload = JWTFactory::make($customClaims);
                        $payloadToken = JWTAuth::encode($payload);
                        $token = 'Bearer '.$payloadToken;

                        // Limpiamos y Guardamos el token en la base de datos
                        UsuariosTokens::where('id_usuario', $usuario->id)->forceDelete();
                        UsuariosTokens::create([
                            'id_usuario'    =>  $usuario->id,
                            'id_rol'        =>  $usuario->rol->id_rol,
                            'id_token'      =>  $payloadToken,
                            'id_creador'    =>  $usuario->id
                        ]);

                        return ['estatus' => true, 'token' => $token];

                    } else {
                        throw new Exception('La contraseña proporcionada no coincide con la del usuario.');
                    }
                } else {
                    throw new Exception('El usuario proporcionado actualmente no se encuenta activo, ponerse en contacto soporte.');
                }
            }
        }
        catch (Exception $e)
        {
            return ['estatus' => false, 'texto' => $e->getMessage(), 'line' => $e->getLine()];
        }
    }

  	/**
  	 * Metodo para Desaser un Inicio de Sesión.
  	 *
  	 * @return Response
  	 */
	  public function logout()
    {
        $body = (Object)Request::all();
        UsuariosTokens::where('id_usuario', $body->usuario->id)->forceDelete();
        return ['estatus' => true, 'texto' => 'El usuario ha cerrado sesión correctamente.'];
    }

    /**
     * Metodo para Verificar una Cuenta Iniciada.
     *
     * @return Response
     */
    public function check()
    {
        $body = (Object)Request::all();
        $usuario = [
            'vc_nombre'     =>  $body->usuario->detalle->vc_nombre,
            'vc_apellido'   =>  $body->usuario->detalle->vc_apellido,
            'rol'           =>  [
                'id'        => $body->usuario->rol->rol->id,
                'vc_nombre' => $body->usuario->rol->rol->vc_nombre
            ]

        ];
        return ['estatus' => true, 'usuario' => $usuario];
    }

}
