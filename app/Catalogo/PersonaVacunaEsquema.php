<?php

namespace App\Catalogo;

use Illuminate\Database\Eloquent\Model;

class PersonaVacunaEsquema extends Model
{
    protected $table = 'personas_vacunas_esquemas';
    
    protected $fillable = ["personas_id","vacunas_esquemas_id","fecha_programada","fecha_aplicacion","fecha_caducidad","lote","dosis","usuario_id"];

    public $timestamps = false;

    public function esquema(){
		  return $this->belongsTo('App\Catalogo\VacunaEsquema', 'vacunas_esquemas_id', 'id')->where('deleted_at', NULL)->with('vacuna')->orderBy('intervalo_inicio', 'ASC')->orderBy('orden_esquema', 'ASC');
	}

}
