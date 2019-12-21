<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model {

    use SoftDeletes;

    const CREATED_AT = 'dt_registro';
    const UPDATED_AT = 'dt_editado';
    const DELETED_AT = 'dt_eliminado';

    protected $hidden = [ 'sn_eliminado', 'dt_editado', 'dt_eliminado' ];

    protected static $rules = []; //Para validaciones

    //protected $dates = ['dt_eliminado'];

   

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = array(static::CREATED_AT, static::UPDATED_AT, static::DELETED_AT );

        return array_merge($this->dates, $defaults);
    }

    /**
     * Update the creation and update timestamps.
     * Modificacion : Solo actualizamos la fecha de "UPDATED_AT"
     *
     * @return void
     */
    protected function updateTimestamps()
    {
        $time = $this->freshTimestamp();

        if ( ! $this->isDirty(static::UPDATED_AT))
        {
            $this->setUpdatedAt($time);
        }
    }



    /**
     * Funciones para el scope
     *
     */
    public function scopeFiltro($query, $delete = 0)
    {
        return $query->where('sn_eliminado', $delete);
    }

    public function scopeFiltroPublico($query, $delete = 0, $active = 1)
    {
        $mainWhere = ['sn_eliminado' => $delete, 'sn_activo' => $active];
        return $query->where($mainWhere);
    }

    public function scopeNoMostrarId($query, $id = 0){
        return $query->where('id', '!=', $id);
    }

    public function scopeEsNombre($query, $name){
        return $query->where('vc_nombre', $name);
    }

    public function scopeEsEmail($query, $name){
        return $query->where('vc_email', $name);
    }

    public function scopeEsId($query, $name){
        return $query->where('id', $name);
    }



    /**
     * Regresa las reglas para validacion
     * Para crear y editar
     * Agrega nuevas reglas al editar
     *
     * @return array
     */
    public static function getRules( $id = null, $merge = array() ) {
        if( is_null($id) )
            return static::$rules;

        $newRules = array_merge( static::$rules, $merge);

        return self::getUpdateRules($id, $newRules);
    }

    /**
     * Return model validation rules for an update
     * Add exception to :unique validations where necessary
     * That means: enforce unique if a unique field changed.
     * But relax unique if a unique field did not change
     *
     * @return array;
     */
    public static function getUpdateRules( $id, $rules ) {
        $updateRules = [];
        foreach( $rules as $field => $rule) {
            $newRule = [];
            // Dividimos la regla por partes
            $ruleParts = explode('|',$rule);

            // Verificamos si hay reglas con "unique"
            foreach($ruleParts as $part) {
                if(strpos($part,'unique:') === 0) {
                    $part = $part . ',' . $field . ',' . $id;
                    // Check if field was unchanged
                    //if ( ! $this->isDirty($field)) {
                        // Field did not change, make exception for this model
                        //$part = $part . ',' . $field . ',' . $this->getAttribute($field) . ',' . $field;
                    //}
                }
                // All other go directly back to the newRule Array
                $newRule[] = $part;
            }
            // Add newRule to updateRules
            $updateRules[$field] = join('|', $newRule);
        }

        return $updateRules;
    }
}
