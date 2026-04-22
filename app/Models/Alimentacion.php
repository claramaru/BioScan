<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alimentacion extends Model
{
    protected $table = 'alimentacion';
    protected $primaryKey = 'id_alimentacion';
    public $timestamps = false;

    protected $fillable = [
        'id_animal',
        'id_pienso',
        'cantidad',
        'fecha',
        'id_usuario',
    ];

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'id_animal', 'id_animal');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function pienso()
    {
        return $this->belongsTo(Pienso::class, 'id_pienso', 'id_pienso');
    }
}
