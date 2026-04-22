<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasFactory;

    protected $table = 'animal';
    protected $primaryKey = 'id_animal';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'especie',
        'raza',
        'id_pienso_recomendado',
        'tipo_pienso_recomendado',
        'lote',
        'fecha_alta',
        'observaciones',
        'id_cebadero',
    ];

    protected $casts = [
        'fecha_alta' => 'date',
    ];

    public function cebadero(): BelongsTo
    {
        return $this->belongsTo(Cebadero::class, 'id_cebadero', 'id_cebadero');
    }

    public function piensoRecomendado(): BelongsTo
    {
        return $this->belongsTo(Pienso::class, 'id_pienso_recomendado', 'id_pienso');
    }

    public function alimentaciones(): HasMany
    {
        return $this->hasMany(Alimentacion::class, 'id_animal', 'id_animal');
    }

    public function fichasMedicas(): HasMany
    {
        return $this->hasMany(FichaMedica::class, 'id_animal', 'id_animal');
    }
}
