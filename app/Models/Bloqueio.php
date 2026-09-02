<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloqueio extends Model
{
    use HasFactory;

    protected $table = 'bloqueios';

    protected $fillable = [
        'data',
        'turno',
        'motivo',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
    ];
}
