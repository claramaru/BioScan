<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = 'animal';
    protected $primaryKey = 'id_animal';
    public $timestamps = false;

    protected $fillable = ['codigo', 'especie', 'raza', 'id_pienso_recomendado', 'lote', 'fecha_alta', 'observaciones', 'id_cebadero'];

    public function cebadero()
    {
        return $this->belongsTo(Cebadero::class, 'id_cebadero', 'id_cebadero');
    }

    public function piensoRecomendado()
    {
        return $this->belongsTo(Pienso::class, 'id_pienso_recomendado', 'id_pienso');
    }
}
