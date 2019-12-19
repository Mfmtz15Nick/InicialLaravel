<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendasHorarios extends BaseModel
{
	// Datos Generales
	protected $table 		= 'agendasHorarios';
	protected $fillable = ['id_agenda', 'nu_dia', 'tm_entrada', 'tm_salida', 'id_creador'];

	// Relacion - Agenda
  public function agenda()
  {
      return $this->belongsTo('App\Models\Agendas', 'id_agenda');
  }
}
