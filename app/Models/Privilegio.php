<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Privilegio extends Model
{
    use HasFactory;

    protected $table = 'privilegio';
    protected $primaryKey = 'id_privilegio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_privilegio', 'id_privilegio', 'id_rol');
    }
}
