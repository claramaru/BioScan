<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Rol;

class Privilegio extends Model
{
    protected $table = 'privilegio';
    protected $primaryKey = 'id_privilegio';
    public $timestamps = false;
    #Saber los roles que tienen ese privilegio
    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'rol_privilegio',
            'id_privilegio',
            'id_rol'
        );
    }
}
