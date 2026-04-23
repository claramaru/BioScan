<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alimentacion extends Model
{
    use HasFactory;

    protected $table = 'alimentacion';
    protected $primaryKey = 'id_alimentacion';
    public $timestamps = false;

    protected $fillable = [
        'id_animal',
        'id_pienso',
        'tipo_pienso',
        'cantidad',
        'fecha',
        'id_usuario',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha' => 'date',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'id_animal', 'id_animal');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function pienso(): BelongsTo
    {
        return $this->belongsTo(Pienso::class, 'id_pienso', 'id_pienso');
    }

    
}
