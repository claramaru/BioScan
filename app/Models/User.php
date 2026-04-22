<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
            'password' => 'hashed',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function tienePrivilegio(string $nombre): bool
    {
        $this->loadMissing('rol.privilegios');

        return $this->rol
            && $this->rol->privilegios->contains('nombre', $nombre);
    }

    public function esAdministrador(): bool
    {
        $this->loadMissing('rol');

        return strtolower((string) optional($this->rol)->nombre) === 'administrador';
    }

    public function esRol(string $nombre): bool
    {
        $this->loadMissing('rol');

        return strtolower((string) optional($this->rol)->nombre) === strtolower($nombre);
    }
}
