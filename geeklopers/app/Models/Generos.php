<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generos extends BaseModel
{
	// Datos Generales
	protected $table = 'generos';
	protected $fillable = ['vc_nombre'];
}
