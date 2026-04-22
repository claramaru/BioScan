<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichaMedica extends Model
{
    protected $table = 'ficha_medica';
    protected $primaryKey = 'id_ficha';
    public $timestamps = false;

    protected $fillable = ['id_animal', 'id_usuario', 'diagnostico', 'tratamiento', 'observaciones', 'fecha'];
}