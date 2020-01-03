<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesEventos extends BaseModel
{
	// Datos Generales
	protected $table 		= 'clientesEventos';
	protected $fillable = ['id_cliente', 'id_evento', 'nu_dia', 'nu_mes', 'tm_entrada', 'tm_salida', 'id_creador'];

	// Relacion - Cliente
  public function cliente()
  {
      return $this->belongsTo('App\Models\Clientes', 'id_cliente');
  }
  public function eventoDetalle()
  {
      return $this->hasOne('App\Models\EventosDetalles', 'id_evento');
  }
  public function nombre()
  {
      return $this->hasOne('App\Models\EventosDetalles', 'id_evento');
  }





  		// Relacion - EventosDetalles
      public function nombreEvento()
      {
          return $this->belongsTo('App\Models\EventosDetalles', 'id_evento', 'id_evento');
      }



  
}
