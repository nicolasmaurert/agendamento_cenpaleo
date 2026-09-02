<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Bloqueio;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with initial demo data.
     */
    public function run(): void
    {
        $hoje = Carbon::now();
        $anoMes = $hoje->format('Y-m');

        // Sample Agendamentos (Yellow / Occupied Days)
        Agendamento::create([
            'data' => "{$anoMes}-10",
            'turno' => 'manha',
            'responsavel' => 'Profa. Regina Duarte',
            'instituicao' => 'Escola Municipal Monteiro Lobato',
            'num_alunos' => 45,
            'telefone' => '(47) 99123-4567',
            'email' => 'regina@escola.edu.br',
            'status' => 'confirmado',
        ]);

        Agendamento::create([
            'data' => "{$anoMes}-10",
            'turno' => 'tarde',
            'responsavel' => 'Prof. Carlos Eduardo',
            'instituicao' => 'Colégio Dom Bosco',
            'num_alunos' => 30,
            'telefone' => '(47) 98877-6655',
            'email' => 'carlos@dombosco.com.br',
            'status' => 'pendente',
        ]);

        Agendamento::create([
            'data' => "{$anoMes}-15",
            'turno' => 'manha',
            'responsavel' => 'Profa. Amanda Souza',
            'instituicao' => 'Instituto Federal de Educação',
            'num_alunos' => 50,
            'telefone' => '(47) 99988-1122',
            'email' => 'amanda.souza@if.edu.br',
            'status' => 'confirmado',
        ]);

        // Sample Bloqueios (Red Days)
        Bloqueio::create([
            'data' => "{$anoMes}-18",
            'turno' => 'ambos',
            'motivo' => 'Manutenção programada no galpão de fósseis',
        ]);

        Bloqueio::create([
            'data' => "{$anoMes}-22",
            'turno' => 'tarde',
            'motivo' => 'Evento corporativo científico institucional',
        ]);
    }
}
