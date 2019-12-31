<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventosDetalles extends BaseModel
{
	// Datos Generales
	protected $table = 'eventosDetalles';
	protected $fillable = [ 'id_evento', 'id_tiposEventos', 'vc_nombre', 'id_creador'];

    // Relacion - Proyectos
    public function eventos()
    {
        return $this->belongsTo('App\Models\Eventos', 'id_evento');
    }

   
    public function tiposEventos()
		{
				return $this->belongsTo('App\Models\TiposEventosDetalles', 'id_tiposEventos', 'id');
		}

}
