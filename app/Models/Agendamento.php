<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $table = 'agendamentos';

    protected $fillable = [
        'data',
        'turno',
        'responsavel',
        'instituicao',
        'num_alunos',
        'telefone',
        'email',
        'status',
        'motivo_cancelamento',
    ];

    protected $casts = [
        'data' => 'date:Y-m-d',
        'num_alunos' => 'integer',
    ];
}
