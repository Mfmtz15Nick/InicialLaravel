<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TiposEventos extends BaseModel
{
	// Datos Generales
	protected $table 		= 'tiposEventos';
	protected $fillable = ['id_tiposEventos'];
   

	// Relacion - ClientessDetalles
    public function detalle()
    {
        return $this->hasOne('App\Models\TiposEventosDetalles', 'id_tiposEventos');
    }


}
