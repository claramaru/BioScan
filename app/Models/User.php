<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidos',
        'email',
        'password',
        'id_rol',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function alimentaciones()
    {
        return $this->hasMany(Alimentacion::class, 'id_usuario', 'id_usuario');
    }

    public function fichasMedicas()
    {
        return $this->hasMany(FichaMedica::class, 'id_usuario', 'id_usuario');
    }

    public function tienePrivilegio(string $nombre): bool
    {
        $this->loadMissing('rol.privilegios');

        return $this->rol !== null
            && $this->rol->privilegios->contains('nombre', $nombre);
    }
}
