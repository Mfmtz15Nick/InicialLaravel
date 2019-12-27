<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientesDetalles extends BaseModel
{
	// Datos Generales
	protected $table = 'clientesDetalles';
	protected $fillable = ['id_cliente', 'vc_nombre', 'vc_apellido', 'nu_celular', 'id_creador'];

    // Relacion - Usuarios
    public function cliente()
    {
        return $this->belongsTo('App\Models\Clientes', 'id_cliente');
    }


    // Relacion - UsuariosDetalles
    public function detalle()
    {
        return $this->hasOne('App\Models\ClientesDetalles', 'id_cliente');
    }

}
