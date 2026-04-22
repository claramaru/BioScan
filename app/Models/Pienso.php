<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pienso extends Model
{
    protected $table = 'pienso';
    protected $primaryKey = 'id_pienso';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    public function alimentaciones()
    {
        return $this->hasMany(Alimentacion::class, 'id_pienso', 'id_pienso');
    }

    public function animalesRecomendados()
    {
        return $this->hasMany(Animal::class, 'id_pienso_recomendado', 'id_pienso');
    }
}
