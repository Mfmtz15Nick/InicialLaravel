<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuariosImagenes extends BaseModel
{
    protected $table    = 'usuariosImagenes';
    protected $fillable = ['id_usuario', 'vc_imagen', 'vc_imagenUrl', 'nu_posicion', 'id_creador'];

    //Relacion - Habitacion
    public function usuario()
    {
        return $this->belongsTo('App\Models\Usuarios', 'id_usuario');
    }
}
