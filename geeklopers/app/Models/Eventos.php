<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends BaseModel
{
	// Datos Generales
	protected $table = 'eventos';

	// Relacion - EventosDetalles
    public function detalle()
    {
        return $this->hasOne('App\Models\EventosDetalles', 'id_evento');
	}    


		// Relacion - imagen
		public function imagen()
		{
				return $this->hasOne('App\Models\EventosImagenes', 'id_evento')->Filtro()->orderBy('nu_posicion');
		}

		// Relacion - imagenes
		public function imagenes()
		{
			return $this->hasMany('App\Models\EventosImagenes', 'id_evento')->Filtro()->orderBy('nu_posicion');
		}
}
