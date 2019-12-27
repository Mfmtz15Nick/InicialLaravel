<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends BaseModel
{
	// Datos Generales
	protected $table 		= 'clientes';
	protected $fillable = ['id_creador'];
   

	// Relacion - ClientessDetalles
    public function detalle()
    {
        return $this->hasOne('App\Models\ClientesDetalles', 'id_cliente');
    }


}
