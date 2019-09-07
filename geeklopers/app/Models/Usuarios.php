<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends BaseModel
{
	// Datos Generales
	protected $table = 'usuarios';

    //Relacion - UsuariosRoles 
    public function rol()
    {
        return $this->hasOne('App\Models\UsuariosRoles', 'id_usuario');
    }

	// Relacion - UsuariosDetalles
    public function detalle()
    {
        return $this->hasOne('App\Models\UsuariosDetalles', 'id_usuario');
    }

    // Relacion - UsuariosFacebook
    public function facebook()
    {
        return $this->hasOne('App\Models\UsuariosFacebook', 'id_usuario');
    }
    
    // Relacion - Clientes
    public function cliente()
    {
        return $this->hasOne('App\Models\Clientes', 'id_usuario');
    }

    // -- HISTORIAL ----------------------------
    
    // Relacion - UsuariosDetalles
    public function detalleHistorial()
    {
        return $this->hasOne('App\Models\UsuariosDetalles', 'id_usuario')
            ->withTrashed();
    }
}
