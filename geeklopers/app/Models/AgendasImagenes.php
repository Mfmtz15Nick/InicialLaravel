<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendasImagenes extends BaseModel
{
	// Datos Generales
	protected $table 		= 'agendasImagenes';
	protected $fillable = ['id_agenda', 'nu_posicion', 'vc_imagen', 'vc_imagenUrl', 'id_creador'];

	// Relacion - Agenda
  public function agenda()
  {
      return $this->belongsTo('App\Models\Agendas', 'id_agenda');
  }
}
