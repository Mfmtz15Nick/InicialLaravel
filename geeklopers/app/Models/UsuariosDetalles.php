<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuariosDetalles extends BaseModel
{
	// Datos Generales
	protected $table = 'usuariosDetalles';
	protected $fillable = ['id_usuario', 'id_genero', 'vc_nombre', 'vc_apellido', 'dt_nacimiento', 'nu_telefono', 'vc_email', 'vc_password', 'dt_nacimiento', 'vc_imagen', 'vc_imagenUrl', 'id_creador'];

    // Relacion - Usuarios
    public function usuario()
    {
        return $this->belongsTo('App\Models\Usuarios', 'id_usuario');
    }

    // Relacion - Generos
    public function genero()
    {
        return $this->belongsTo('App\Models\Generos', 'id_genero');
    }
}
