<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Bloqueio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Exibe formulário de login administrativo.
     */
    public function showLogin()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Processa a autenticação do administrador usando as credenciais do .env.
     */
    public function login(Request $request)
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
    public function logout(Request $request)
    {
        $request->session()->forget(['admin_logged_in', 'admin_user']);
        return redirect()->route('admin.login')->with('success', 'Sessão encerrada com sucesso.');
    }

    /**
     * Painel administrativo com listagem de agendamentos e bloqueios.
     */
    public function dashboard(Request $request)
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

        $query = Agendamento::whereBetween('data', [$inicioMes, $fimMes]);

        if ($status !== 'todos') {
            $query->where('status', $status);
        }

        $agendamentos = $query->orderBy('data', 'asc')->orderBy('turno', 'asc')->get();
        $bloqueios = Bloqueio::whereBetween('data', [$inicioMes, $fimMes])->orderBy('data', 'asc')->get();

        // Estatísticas rápidas
        $stats = [
            'total' => Agendamento::whereBetween('data', [$inicioMes, $fimMes])->count(),
            'pendentes' => Agendamento::whereBetween('data', [$inicioMes, $fimMes])->where('status', 'pendente')->count(),
            'confirmados' => Agendamento::whereBetween('data', [$inicioMes, $fimMes])->where('status', 'confirmado')->count(),
            'cancelados' => Agendamento::whereBetween('data', [$inicioMes, $fimMes])->where('status', 'cancelado')->count(),
            'bloqueios' => Bloqueio::whereBetween('data', [$inicioMes, $fimMes])->count(),
        ];

        return view('admin.dashboard', compact('agendamentos', 'bloqueios', 'mes', 'status', 'stats'));
    }

    /**
     * Atualiza o status de um agendamento (confirmado / cancelado).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pendente,confirmado,cancelado'
        ]);

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->status = $request->input('status');
        $agendamento->save();

        return back()->with('success', "Agendamento #{$id} alterado para '{$agendamento->status}'!");
    }

    /**
     * Cria um novo bloqueio de data/turno.
     */
    public function bloquear(Request $request)
    {
        $request->validate([
            'data' => 'required|date',
            'turno' => 'required|in:manha,tarde,ambos',
            'motivo' => 'nullable|string|max:255',
        ]);

        Bloqueio::create([
            'data' => $request->input('data'),
            'turno' => $request->input('turno'),
            'motivo' => $request->input('motivo', 'Manutenção ou evento interno'),
        ]);

        return back()->with('success', 'Bloqueio de data registrado com sucesso!');
    }

    /**
     * Remove um bloqueio de data/turno.
     */
    public function desbloquear($id)
    {
        $bloqueio = Bloqueio::findOrFail($id);
        $bloqueio->delete();

        return back()->with('success', 'Bloqueio removido com sucesso!');
    }
}
