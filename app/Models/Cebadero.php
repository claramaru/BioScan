<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cebadero extends Model
{
    use HasFactory;

    protected $table = 'cebadero';
    protected $primaryKey = 'id_cebadero';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'ubicacion',
    ];

    public function animales()
    {
        return $this->hasMany(Animal::class, 'id_cebadero', 'id_cebadero');
    }
}
