<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TiposEventosDetalles extends BaseModel
{
    protected $table = 'tiposEventosDetalles';
    protected $fillable = [ 'id_tiposEventos', 'vc_nombre', 'vc_imagen', 'vc_imagenUrl', 'id_creador' ];

    public function tiposEventos()
    {
        return $this->hasMany('App\Models\TiposEventosDetalles', 'id_tiposEventos');
    }


}
