<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Citas extends BaseModel
{
	// Datos Generales
	protected $table 		= 'citas';
	protected $fillable = [ 'id_agenda',
                          'vc_nombre',
                          'dt_fecha',
                          'id_creador'];

  // Relacion - Agenda
  public function agenda()
  {
      return $this->belongsTo('App\Models\Agendas', 'id_agenda');
  }
}
