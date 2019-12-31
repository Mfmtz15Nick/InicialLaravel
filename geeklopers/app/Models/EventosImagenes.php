<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventosImagenes extends BaseModel
{
	// Datos Generales
	protected $table = 'eventosImagenes';
  protected $fillable = [ 'id_evento', 'nu_posicion', 'vc_imagen', 'vc_imagenUrl'];

  // Relacion - proyecto
  public function evento()
  {
      return $this->belongsTo('App\Models\Eventos', 'id_evento');
  }
}
