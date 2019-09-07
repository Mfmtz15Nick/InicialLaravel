<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuariosRoles extends BaseModel
{
	// Datos Generales
	protected $table = 'usuariosRoles';
    protected $fillable = ['id_usuario', 'id_rol', 'id_creador'];
    
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
