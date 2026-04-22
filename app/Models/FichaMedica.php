<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaMedica extends Model
{
    use HasFactory;

    protected $table = 'ficha_medica';
    protected $primaryKey = 'id_ficha';
    public $timestamps = false;

    protected $fillable = [
        'id_animal',
        'id_usuario',
        'diagnostico',
        'tratamiento',
        'observaciones',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'id_animal', 'id_animal');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
