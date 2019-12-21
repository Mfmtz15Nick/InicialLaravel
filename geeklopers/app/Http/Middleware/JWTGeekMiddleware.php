<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

use App\Models\UsuariosDispositivos as Dispositivos;
use App\Models\Usuarios;

use Response;
use Validator;

use JWTAuth;
use JWTFactory;

use Carbon\Carbon;

use App\Models\UsuariosTokens;

class JWTGeekMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {

        if (!$token = $this->auth->setRequest($request)->getToken() ) {
            return Response::json(['texto' => 'Actualmente no cuentas con un token.'], 404);
        }

        try {

            // Obtener los claims
            $claims = JWTAuth::getJWTProvider()->decode($token);

            // Verificar si el token esta expirado
            if (Carbon::now()->timestamp >= Carbon::createFromTimestamp($claims['exp'])->timestamp) {
                throw new TokenExpiredException('El token proporcionado ha expirado.', 1);
            }

            // Verificar el tipo de Token administrativo o cliente
            if (isset($claims['key'])) {
                if ($claims['key'] != env('APP_KEY', 'base64:75AuRXSkz6uzmfK3yTCZcV/INZSRUaEF01QurB63QKs=') || !isset($claims['rol']) || ($claims['rol'] != 2 && $claims['rol'] != 6)) {
                    return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 401);
                }
            } else {
                // Verificar autenticacion del token
                if (Carbon::now()->timestamp > $claims['exp']) {
                    throw new TokenExpiredException('El token proporcionado ha expirado.', 400);
                }
            }

            // Obtener el usuario
            $usuario = Usuarios::with('detalle', 'rol.rol')->find($claims['sub']);

            // Validamos que el Usuario exista
            if (!$usuario) {
                return Response::json(['texto' => 'Actualemnte el usuario no encontrado registrado.'], 404);
            }

            // Asignamos al usuario al request
            $request->merge(['usuario' => $usuario ]);
            $request->setUserResolver(function () use ($usuario) {
                return $usuario;
            });

            return $next($request);

        } catch (TokenExpiredException $e) {

            // Obtener los claims
            $claims = JWTAuth::getJWTProvider()->decode($token);

            // Verificamos los registros para ver si existe este token
            if (!UsuariosTokens::where('id_usuario', $claims['sub'])->where('id_token', $token)->count()) {
                return Response::json(['texto' => 'Actualmente no cuenta con los permisos necesarios.'], 401);
            }

            // Proceso para generar un token en base a los registros
            $tm_carbon = Carbon::now();

            $claims['iat'] = $tm_carbon->timestamp;
            $claims['exp'] = $tm_carbon->addHours(env('TOKEN_EXPIRED_ADMIN', '24'))->timestamp;
            $payload = JWTFactory::make($claims);
            $payloadToken = JWTAuth::encode($payload);
            $newToken = 'Bearer '.$payloadToken;

            // Obtener el usuario
            $usuario = Usuarios::with('detalle', 'rol.rol')->find($claims['sub']);

            // Validamos que el Usuario exista
            if (!$usuario) {
                return Response::json(['texto' => 'Actualemnte el usuario no encontrado registrado.'], 404);
            }

            // Limpiamos y Guardamos el token en la base de datos
            UsuariosTokens::where('id_usuario', $usuario->id)->forceDelete();
            UsuariosTokens::create([
                'id_usuario'    =>  $usuario->id,
                'id_rol'        =>  $usuario->rol->id_rol,
                'id_token'      =>  $payloadToken,
                'id_creador'    =>  $usuario->id
            ]);

            // Asignamos al usuario al request
            $request->merge(['usuario' => $usuario ]);
            $request->setUserResolver(function () use ($usuario) {
                return $usuario;
            });

            // Continuamos con la peticion
            $response = $next($request);

            // Mandamos el token nuevo en el header
            $response->headers->set('Authorization', $newToken);

            return $response;

        } catch (JWTException $e) {
            return Response::json(['texto' => 'El token proporcionado es invalido.'], 401);
        }
    }
}
