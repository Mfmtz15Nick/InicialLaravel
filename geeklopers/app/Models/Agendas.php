<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendas extends BaseModel
{
	// Datos Generales
	protected $table 		= 'agendas';
	protected $fillable = ['vc_nombre', 'id_creador'];

	// Relacion - Horarios
    public function horarios()
    {
        return $this->hasMany('App\Models\AgendasHorarios', 'id_agenda');
    }
}
