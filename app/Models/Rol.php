<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Privilegio;


class Rol extends Model
{
    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    public function privilegios()
    {
        return $this->belongsToMany(
            Privilegio::class,
            'rol_privilegio',
            'id_rol',
            'id_privilegio'
        );
    }
}