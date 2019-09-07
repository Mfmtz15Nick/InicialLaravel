<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends BaseModel
{
	// Datos Generales
	protected $table = 'roles';
	protected $fillable = ['vc_nombre'];
}
