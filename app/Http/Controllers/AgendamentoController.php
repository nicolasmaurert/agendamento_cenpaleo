<?php

namespace App\Http\Controllers;

use App\Mail\AgendamentoConfirmadoMail;
use App\Models\Agendamento;
use App\Models\Bloqueio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AgendamentoController extends Controller
{
    /**
     * Retorna a disponibilidade diária e por turno de um determinado mês.
     * Endpoint: GET /api/disponibilidade?mes=YYYY-MM
     */
    public function disponibilidade(Request $request)
    {
        $mesInput = $request->query('mes', Carbon::now()->format('Y-m'));

        try {
            $dataBase = Carbon::createFromFormat('Y-m', $mesInput)->startOfMonth();
        } catch (\Exception $e) {
            $dataBase = Carbon::now()->startOfMonth();
            $mesInput = $dataBase->format('Y-m');
        }

        $inicioMes = $dataBase->copy()->startOfMonth()->format('Y-m-d');
        $fimMes = $dataBase->copy()->endOfMonth()->format('Y-m-d');

        // Busca agendamentos ativos no mês
        $agendamentos = Agendamento::whereBetween('data', [$inicioMes, $fimMes])
            ->whereIn('status', ['pendente', 'confirmado'])
            ->get();

        // Busca bloqueios no mês
        $bloqueios = Bloqueio::whereBetween('data', [$inicioMes, $fimMes])->get();

        $diasNoMes = $dataBase->daysInMonth;
        $resultado = [];

        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataStr = $dataBase->copy()->day($dia)->format('Y-m-d');
            $dataCarbon = Carbon::parse($dataStr);

            // Verifica agendamentos do dia
            $agManha = $agendamentos->where('data', $dataStr)->where('turno', 'manha')->first();
            $agTarde = $agendamentos->where('data', $dataStr)->where('turno', 'tarde')->first();

            // Verifica bloqueios do dia
            $blManha = $bloqueios->where('data', $dataStr)->filter(fn($b) => in_array($b->turno, ['manha', 'ambos']))->first();
            $blTarde = $bloqueios->where('data', $dataStr)->filter(fn($b) => in_array($b->turno, ['tarde', 'ambos']))->first();

            $statusManha = $blManha ? 'bloqueado' : ($agManha ? 'agendado' : 'livre');
            $statusTarde = $blTarde ? 'bloqueado' : ($agTarde ? 'agendado' : 'livre');

            // Determina a cor/status geral do dia no calendário
            // Verde: Pelo menos um turno livre
            // Amarelo: Pelo menos um agendamento e nenhum livre (ou 1 agendado + 1 bloqueado)
            // Vermelho: Ambos bloqueados ou final de semana sem expediente
            $isWeekend = $dataCarbon->isWeekend();

            if ($isWeekend) {
                $statusDia = 'vermelho'; // Fim de semana desativado por padrão
                $motivo = 'Sem expediente no fim de semana';
            } elseif ($statusManha === 'livre' || $statusTarde === 'livre') {
                $statusDia = 'verde';
                $motivo = 'Turnos disponíveis';
            } elseif ($statusManha === 'bloqueado' && $statusTarde === 'bloqueado') {
                $statusDia = 'vermelho';
                $motivo = 'Dia totalmente bloqueado';
            } elseif ($statusManha === 'agendado' && $statusTarde === 'agendado') {
                $statusDia = 'amarelo';
                $motivo = 'Todos os turnos agendados';
            } else {
                $statusDia = 'amarelo';
                $motivo = 'Ocupado ou parcialmente bloqueado';
            }

            $resultado[$dataStr] = [
                'data' => $dataStr,
                'dia' => $dia,
                'dia_semana' => $dataCarbon->dayOfWeek,
                'is_weekend' => $isWeekend,
                'status_dia' => $statusDia,
                'motivo' => $motivo,
                'turnos' => [
                    'manha' => [
                        'status' => $statusManha,
                        'detalhe' => $blManha ? ($blManha->motivo ?? 'Bloqueado pela administração') : ($agManha ? 'Agendado' : 'Disponível'),
                    ],
                    'tarde' => [
                        'status' => $statusTarde,
                        'detalhe' => $blTarde ? ($blTarde->motivo ?? 'Bloqueado pela administração') : ($agTarde ? 'Agendado' : 'Disponível'),
                    ]
                ]
            ];
        }

        return response()->json([
            'mes' => $mesInput,
            'dias' => $resultado
        ]);
    }

    /**
     * Realiza um novo agendamento público garantindo transação atômica contra race conditions.
     * Endpoint: POST /api/agendar
     */
    public function agendar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'responsavel' => 'required|string|max:255',
            'instituicao' => 'required|string|max:255',
            'data' => 'required|date|after_or_equal:today',
            'turno' => 'required|in:manha,tarde',
            'num_alunos' => 'required|integer|min:1|max:500',
            'telefone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'lgpd' => 'required|accepted',
        ], [
            'responsavel.required' => 'O nome do responsável é obrigatório.',
            'instituicao.required' => 'O nome da instituição é obrigatório.',
            'data.required' => 'A data da visita é obrigatória.',
            'data.after_or_equal' => 'A data da visita não pode ser retroativa.',
            'turno.required' => 'Selecione o turno (Manhã ou Tarde).',
            'num_alunos.required' => 'Informe o número de alunos.',
            'num_alunos.min' => 'O número mínimo é 1 aluno.',
            'telefone.required' => 'O telefone é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Forneça um e-mail válido.',
            'lgpd.accepted' => 'Você deve concordar com os termos de privacidade (LGPD).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Evitar agendamentos no fim de semana
        $dataCarbon = Carbon::parse($validated['data']);
        if ($dataCarbon->isWeekend()) {
            return response()->json([
                'success' => false,
                'message' => 'Agendamentos não estão disponíveis aos fins de semana.'
            ], 422);
        }

        try {
            // TRANSAÇÃO ATÔMICA E LOCK FOR UPDATE PARA EVITAR RACE CONDITION (CONCORRÊNCIA SIMULTÂNEA)
            $resultado = DB::transaction(function () use ($validated) {
                // 1. Verifica agendamento conflitante ativo com LOCK FOR UPDATE
                $agendamentoExistente = Agendamento::where('data', $validated['data'])
                    ->where('turno', $validated['turno'])
                    ->whereIn('status', ['pendente', 'confirmado'])
                    ->lockForUpdate()
                    ->first();

                if ($agendamentoExistente) {
                    return [
                        'status_code' => 409,
                        'response' => [
                            'success' => false,
                            'message' => 'Atenção: O turno ' . strtoupper($validated['turno']) . ' do dia ' . Carbon::parse($validated['data'])->format('d/m/Y') . ' acabou de ser reservado por outro visitante.'
                        ]
                    ];
                }

                // 2. Verifica se há bloqueio administrativo ativo com LOCK FOR UPDATE
                $bloqueioExistente = Bloqueio::where('data', $validated['data'])
                    ->where(function ($q) use ($validated) {
                        $q->where('turno', $validated['turno'])
                          ->orWhere('turno', 'ambos');
                    })
                    ->lockForUpdate()
                    ->first();

                if ($bloqueioExistente) {
                    return [
                        'status_code' => 409,
                        'response' => [
                            'success' => false,
                            'message' => 'O turno selecionado foi bloqueado para manutenção ou evento institucional.'
                        ]
                    ];
                }

                // 3. Salva o agendamento
                $agendamento = Agendamento::create([
                    'data' => $validated['data'],
                    'turno' => $validated['turno'],
                    'responsavel' => $validated['responsavel'],
                    'instituicao' => $validated['instituicao'],
                    'num_alunos' => $validated['num_alunos'],
                    'telefone' => $validated['telefone'],
                    'email' => $validated['email'],
                    'status' => 'pendente',
                ]);

                // 4. Disparo de e-mail de confirmação (com log fallback)
                try {
                    Mail::to($agendamento->email)->send(new AgendamentoConfirmadoMail($agendamento));
                } catch (\Exception $e) {
                    Log::warning('Erro no disparo de e-mail de agendamento: ' . $e->getMessage());
                }

                return [
                    'status_code' => 201,
                    'response' => [
                        'success' => true,
                        'message' => 'Agendamento solicitado com sucesso! Enviamos um e-mail de confirmação.',
                        'agendamento' => $agendamento
                    ]
                ];
            });

            return response()->json($resultado['response'], $resultado['status_code']);

        } catch (\Exception $e) {
            Log::error('Erro ao processar agendamento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro interno ao salvar o agendamento. Tente novamente.'
            ], 500);
        }
    }
}
