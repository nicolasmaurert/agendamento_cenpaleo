<?php

namespace App\Http\Controllers;

use App\Mail\AgendamentoStatusMail;
use App\Models\Agendamento;
use App\Models\Bloqueio;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AdminController extends Controller
{
    /**
     * Exibe formulário de login administrativo.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Processa a autenticação do administrador usando as credenciais do .env.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $envUser = config('app.admin_username', env('ADMIN_USERNAME', 'admin'));
        $envPass = config('app.admin_password', env('ADMIN_PASSWORD', 'cenpaleo2026'));

        if ($request->input('username') === $envUser && $request->input('password') === $envPass) {
            session(['admin_logged_in' => true, 'admin_user' => $envUser]);

            return redirect()->route('admin.dashboard')->with('success', 'Login realizado com sucesso!');
        }

        return back()->withErrors(['login' => 'Credenciais incorretas. Verifique seu usuário e senha.'])->withInput();
    }

    /**
     * Encerra a sessão do administrador.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['admin_logged_in', 'admin_user']);

        return redirect()->route('admin.login')->with('success', 'Sessão encerrada com sucesso.');
    }

    /**
     * Painel administrativo com listagem de agendamentos e bloqueios.
     */
    public function dashboard(Request $request): View
    {
        $mes = $request->query('mes', Carbon::now()->format('Y-m'));
        $status = $request->query('status', 'todos');

        try {
            $dataBase = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        } catch (\Exception $e) {
            $dataBase = Carbon::now()->startOfMonth();
            $mes = $dataBase->format('Y-m');
        }

        $inicioMes = $dataBase->copy()->startOfMonth()->format('Y-m-d');
        $fimMes = $dataBase->copy()->endOfMonth()->format('Y-m-d');

        $agendamentosDoMes = Agendamento::whereBetween('data', [$inicioMes, $fimMes])
            ->orderBy('data', 'asc')
            ->orderBy('turno', 'asc')
            ->get();

        $agendamentos = $status === 'todos'
            ? $agendamentosDoMes
            : $agendamentosDoMes->where('status', $status)->values();

        $bloqueios = Bloqueio::whereBetween('data', [$inicioMes, $fimMes])->orderBy('data', 'asc')->get();

        // Estatísticas rápidas
        $stats = [
            'total' => $agendamentosDoMes->count(),
            'pendentes' => $agendamentosDoMes->where('status', 'pendente')->count(),
            'confirmados' => $agendamentosDoMes->where('status', 'confirmado')->count(),
            'cancelados' => $agendamentosDoMes->where('status', 'cancelado')->count(),
            'bloqueios' => $bloqueios->count(),
        ];

        $diasCalendario = $this->construirCalendario(
            $dataBase,
            $agendamentosDoMes->whereIn('status', ['pendente', 'confirmado']),
            $bloqueios
        );
        $primeiroDiaSemana = $dataBase->copy()->startOfMonth()->dayOfWeek;
        $mesAnterior = $dataBase->copy()->subMonth()->format('Y-m');
        $proximoMes = $dataBase->copy()->addMonth()->format('Y-m');
        $mesLabel = ucfirst($dataBase->translatedFormat('F \d\e Y'));

        return view('admin.dashboard', compact(
            'agendamentos',
            'bloqueios',
            'diasCalendario',
            'primeiroDiaSemana',
            'mes',
            'mesAnterior',
            'proximoMes',
            'mesLabel',
            'status',
            'stats'
        ));
    }

    /**
     * Atualiza o status de um agendamento (confirmado / cancelado).
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:confirmado,cancelado',
            'motivo_cancelamento' => 'required_if:status,cancelado|nullable|string|min:5|max:1000',
        ], [
            'motivo_cancelamento.required_if' => 'Informe o motivo da negação ou do cancelamento para que ele seja enviado ao cliente.',
            'motivo_cancelamento.min' => 'O motivo deve ter pelo menos 5 caracteres.',
            'motivo_cancelamento.max' => 'O motivo não pode ultrapassar 1000 caracteres.',
        ]);

        $resultado = DB::transaction(function () use ($id, $validated): array {
            $agendamento = Agendamento::query()->lockForUpdate()->findOrFail($id);
            $statusAnterior = $agendamento->status;

            if ($statusAnterior === $validated['status']) {
                return [
                    'agendamento' => $agendamento,
                    'tipoEmail' => null,
                    'mensagem' => 'O agendamento já estava com o status selecionado.',
                ];
            }

            $tipoEmail = $validated['status'] === 'confirmado'
                ? 'confirmacao'
                : ($statusAnterior === 'pendente' ? 'negacao' : 'cancelamento');

            $agendamento->status = $validated['status'];
            $agendamento->motivo_cancelamento = $validated['status'] === 'cancelado'
                ? trim($validated['motivo_cancelamento'])
                : null;
            $agendamento->save();

            $mensagem = match ($tipoEmail) {
                'confirmacao' => 'Agendamento confirmado e e-mail de confirmação colocado na fila de envio.',
                'negacao' => 'Solicitação negada e e-mail com o motivo colocado na fila de envio.',
                'cancelamento' => 'Agendamento cancelado e e-mail com o motivo colocado na fila de envio.',
            };

            return compact('agendamento', 'tipoEmail', 'mensagem');
        }, attempts: 3);

        if ($resultado['tipoEmail'] === null) {
            return back()->with('success', $resultado['mensagem']);
        }

        try {
            Mail::to($resultado['agendamento']->email)->queue(
                new AgendamentoStatusMail($resultado['agendamento'], $resultado['tipoEmail'])
            );
        } catch (Throwable $exception) {
            Log::error('Não foi possível enfileirar o e-mail de atualização do agendamento.', [
                'agendamento_id' => $resultado['agendamento']->id,
                'exception' => $exception,
            ]);

            return back()->with('warning', 'O status foi atualizado, mas o e-mail não pôde ser colocado na fila. Verifique a configuração de e-mail e tente novamente.');
        }

        return back()->with('success', $resultado['mensagem']);
    }

    /**
     * Cria um novo bloqueio de data/turno.
     */
    public function bloquear(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'turno' => 'required|in:manha,tarde,ambos',
            'motivo' => 'nullable|string|max:255',
        ]);

        $turnosBloqueados = $validated['turno'] === 'ambos'
            ? ['manha', 'tarde']
            : [$validated['turno']];

        $agendamentoConflitante = DB::transaction(function () use ($validated, $turnosBloqueados): ?Agendamento {
            $agendamento = Agendamento::where('data', $validated['data'])
                ->whereIn('status', ['pendente', 'confirmado'])
                ->where(function ($query) use ($turnosBloqueados) {
                    $query->whereIn('turno', $turnosBloqueados)
                        ->orWhere('turno', 'ambos');
                })
                ->lockForUpdate()
                ->first();

            if ($agendamento) {
                return $agendamento;
            }

            Bloqueio::updateOrCreate(
                [
                    'data' => $validated['data'],
                    'turno' => $validated['turno'],
                ],
                [
                    'motivo' => ($validated['motivo'] ?? null) ?: 'Manutenção ou evento interno',
                ]
            );

            return null;
        }, attempts: 3);

        if ($agendamentoConflitante) {
            $dataFormatada = $agendamentoConflitante->data->format('d/m/Y');
            $periodoFormatado = $agendamentoConflitante->turno === 'ambos'
                ? 'nos turnos da manhã e da tarde'
                : 'no turno da '.($agendamentoConflitante->turno === 'manha' ? 'manhã' : 'tarde');

            return back()
                ->withErrors([
                    'bloqueio' => "Não é possível bloquear {$dataFormatada} {$periodoFormatado}: há uma visita de {$agendamentoConflitante->responsavel} com status {$agendamentoConflitante->status}. Cancele primeiro o agendamento do cliente e tente novamente.",
                ])
                ->withInput();
        }

        return back()->with('success', 'Bloqueio de data registrado com sucesso!');
    }

    /**
     * Remove um bloqueio de data/turno.
     */
    public function desbloquear(int $id): RedirectResponse
    {
        $bloqueio = Bloqueio::findOrFail($id);
        $bloqueio->delete();

        return back()->with('success', 'Bloqueio removido com sucesso!');
    }

    /**
     * Monta os dias do calendário administrativo com as visitas e os bloqueios de cada turno.
     *
     * @param  Collection<int, Agendamento>  $agendamentos
     * @param  Collection<int, Bloqueio>  $bloqueios
     * @return array<int, array{
     *     data: string,
     *     dia: int,
     *     fim_de_semana: bool,
     *     turnos: array<string, array{agendamento: Agendamento|null, bloqueio: Bloqueio|null}>
     * }>
     */
    private function construirCalendario(Carbon $mes, Collection $agendamentos, Collection $bloqueios): array
    {
        $agendamentosPorData = $agendamentos->groupBy(
            fn (Agendamento $agendamento): string => $agendamento->data->format('Y-m-d')
        );
        $bloqueiosPorData = $bloqueios->groupBy(
            fn (Bloqueio $bloqueio): string => $bloqueio->data->format('Y-m-d')
        );
        $dias = [];

        for ($dia = 1; $dia <= $mes->daysInMonth; $dia++) {
            $data = $mes->copy()->day($dia);
            $dataChave = $data->format('Y-m-d');
            $agendamentosDoDia = $agendamentosPorData->get($dataChave, collect());
            $bloqueiosDoDia = $bloqueiosPorData->get($dataChave, collect());
            $turnos = [];

            foreach (['manha', 'tarde'] as $turno) {
                $turnos[$turno] = [
                    'agendamento' => $agendamentosDoDia->first(
                        fn (Agendamento $agendamento): bool => in_array($agendamento->turno, [$turno, 'ambos'], true)
                    ),
                    'bloqueio' => $bloqueiosDoDia->first(
                        fn (Bloqueio $bloqueio): bool => in_array($bloqueio->turno, [$turno, 'ambos'], true)
                    ),
                ];
            }

            $dias[] = [
                'data' => $dataChave,
                'dia' => $dia,
                'fim_de_semana' => $data->isWeekend(),
                'turnos' => $turnos,
            ];
        }

        return $dias;
    }
}
