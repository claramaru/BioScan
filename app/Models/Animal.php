<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    protected $table = 'animal';
    protected $primaryKey = 'id_animal';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'especie',
        'tipo_pienso_recomendado',
        'lote',
        'fecha_alta',
        'observaciones',
        'id_cebadero',
    ];

    protected $casts = [
        'fecha_alta' => 'date',
    ];

    public function cebadero()
    {
        return $this->belongsTo(Cebadero::class, 'id_cebadero', 'id_cebadero');
    }

    public function alimentaciones()
    {
        return $this->hasMany(Alimentacion::class, 'id_animal', 'id_animal');
    }

    public function fichasMedicas()
    {
        return $this->hasMany(FichaMedica::class, 'id_animal', 'id_animal');
    }
}
