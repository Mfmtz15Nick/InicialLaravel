<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuariosTokens extends BaseModel
{
	// Datos Generales
	protected $table = 'usuariosTokens';
	protected $fillable = ['id_usuario', 'id_rol', 'id_token', 'id_dispositivo', 'id_creador'];
    
    // Relacion - Usuarios
    public function usuario()
    {
        return $this->belongsTo('App\Models\Usuarios', 'id_usuario');
    }
    
    // Relacion - Roles
    public function rol()
    {
        return $this->belongsTo('App\Models\Roles', 'id_rol');
    }

}
