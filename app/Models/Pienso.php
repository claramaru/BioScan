<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pienso extends Model
{
    use HasFactory;

    protected $table = 'pienso';
    protected $primaryKey = 'id_pienso';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function animalesRecomendados(): HasMany
    {
        return $this->hasMany(Animal::class, 'id_pienso_recomendado', 'id_pienso');
    }

    public function alimentaciones(): HasMany
    {
        return $this->hasMany(Alimentacion::class, 'id_pienso', 'id_pienso');
    }
}
