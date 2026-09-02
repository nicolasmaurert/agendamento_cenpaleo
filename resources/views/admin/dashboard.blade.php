@extends('layouts.app')

@section('title', 'Painel Administrativo - Cenpaleo')

@section('content')
<div class="space-y-8">

    <!-- Top Bar Header -->
    <div class="glass-panel rounded-3xl p-6 border border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-xl">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-2xl font-extrabold text-white tracking-tight">Painel de Gestão de Agendamentos</h2>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold border border-emerald-500/30">Admin</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Gerencie solicitações de visitas, confirme presença e controle dias bloqueados da Exposição Cenpaleo.</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="openBloqueioModal()" class="px-4 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/40 text-xs font-bold transition-all flex items-center gap-2">
                <i data-lucide="ban" class="w-4 h-4"></i>
                <span>Bloquear Data / Turno</span>
            </button>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-semibold transition-all flex items-center gap-2">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Sair</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-600/60 text-emerald-300 text-sm font-semibold flex items-center gap-3 shadow-lg">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-slate-800 space-y-1">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Mês</span>
            <div class="text-2xl font-extrabold text-white">{{ $stats['total'] }}</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-slate-800 space-y-1">
            <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Pendentes</span>
            <div class="text-2xl font-extrabold text-amber-300">{{ $stats['pendentes'] }}</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-slate-800 space-y-1">
            <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Confirmados</span>
            <div class="text-2xl font-extrabold text-emerald-300">{{ $stats['confirmados'] }}</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-slate-800 space-y-1">
            <span class="text-xs font-semibold text-rose-400 uppercase tracking-wider">Cancelados</span>
            <div class="text-2xl font-extrabold text-rose-300">{{ $stats['cancelados'] }}</div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-slate-800 space-y-1 col-span-2 sm:col-span-1">
            <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Bloqueios</span>
            <div class="text-2xl font-extrabold text-purple-300">{{ $stats['bloqueios'] }}</div>
        </div>
    </div>

    <!-- Filters & Table Section -->
    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl space-y-6">
        
        <!-- Filter Header -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 pb-4 border-b border-slate-800">
            <!-- Month Selector -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-3">
                <input type="hidden" name="status" value="{{ $status }}">
                <label for="mes-select" class="text-xs font-bold text-slate-300 uppercase tracking-wider">Mês:</label>
                <input type="month" id="mes-select" name="mes" value="{{ $mes }}" onchange="this.form.submit()"
                       class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-semibold focus:outline-none focus:border-emerald-500">
            </form>

            <!-- Status Tabs -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-900/80 rounded-xl border border-slate-800 overflow-x-auto w-full md:w-auto">
                @foreach(['todos' => 'Todos', 'pendente' => 'Pendentes', 'confirmado' => 'Confirmados', 'cancelado' => 'Cancelados'] as $key => $label)
                    <a href="{{ route('admin.dashboard', ['mes' => $mes, 'status' => $key]) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all shrink-0 {{ $status === $key ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Agendamentos Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 uppercase tracking-wider border-b border-slate-800 text-[11px]">
                    <tr>
                        <th class="py-3 px-4"># ID</th>
                        <th class="py-3 px-4">Data & Turno</th>
                        <th class="py-3 px-4">Responsável / Instituição</th>
                        <th class="py-3 px-4">Alunos</th>
                        <th class="py-3 px-4">Contato</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-200">
                    @forelse($agendamentos as $ag)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-slate-400">#{{ $ag->id }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}</div>
                                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ag->turno === 'manha' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' }}">
                                    {{ ucfirst($ag->turno) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">{{ $ag->responsavel }}</div>
                                <div class="text-slate-400 text-[11px]">{{ $ag->instituicao }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-emerald-400">{{ $ag->num_alunos }}</span> alunos
                            </td>
                            <td class="py-3.5 px-4 space-y-0.5">
                                <div class="flex items-center gap-1 text-slate-300">
                                    <i data-lucide="phone" class="w-3 h-3 text-slate-400"></i>
                                    <span>{{ $ag->telefone }}</span>
                                </div>
                                <div class="flex items-center gap-1 text-slate-400 text-[11px]">
                                    <i data-lucide="mail" class="w-3 h-3 text-slate-400"></i>
                                    <span>{{ $ag->email }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ag->status === 'pendente')
                                    <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold">Pendente</span>
                                @elseif($ag->status === 'confirmado')
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold">Confirmado</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-rose-500/20 text-rose-300 border border-rose-500/40 font-bold">Cancelado</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($ag->status !== 'confirmado')
                                        <form action="{{ route('admin.agendamentos.update', $ag->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="confirmado">
                                            <button type="submit" title="Confirmar Agendamento" class="p-2 rounded-lg bg-emerald-600/20 hover:bg-emerald-600/40 text-emerald-300 border border-emerald-500/30 transition-all">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($ag->status !== 'cancelado')
                                        <form action="{{ route('admin.agendamentos.update', $ag->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelado">
                                            <button type="submit" title="Cancelar Agendamento" onclick="return confirm('Deseja realmente cancelar este agendamento?')" class="p-2 rounded-lg bg-rose-600/20 hover:bg-rose-600/40 text-rose-300 border border-rose-500/30 transition-all">
                                                <i data-lucide="ban" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-600"></i>
                                <p>Nenhum agendamento encontrado para o mês/status selecionado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bloqueios Registrados Section -->
    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-ban" class="w-5 h-5 text-rose-400"></i>
                <h3 class="text-lg font-bold text-white">Datas / Turnos Bloqueados no Mês</h3>
            </div>
            <span class="text-xs text-slate-400">{{ count($bloqueios) }} bloqueio(s) ativo(s)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse($bloqueios as $bl)
                <div class="glass-card p-4 rounded-2xl border border-slate-800 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-white text-sm">{{ \Carbon\Carbon::parse($bl->data)->format('d/m/Y') }}</div>
                        <div class="text-xs text-rose-400 font-semibold uppercase mt-0.5">Turno: {{ ucfirst($bl->turno) }}</div>
                        <div class="text-[11px] text-slate-400 mt-1 italic">Motivo: {{ $bl->motivo ?: 'Não especificado' }}</div>
                    </div>
                    <form action="{{ route('admin.desbloquear', $bl->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Remover Bloqueio" onclick="return confirm('Remover este bloqueio e liberar a data?')" class="p-2 rounded-xl bg-slate-800 hover:bg-rose-950 text-slate-400 hover:text-rose-300 border border-slate-700 hover:border-rose-700 transition-all">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            @empty
                <div class="col-span-full py-6 text-center text-slate-500 text-xs">
                    Nenhum bloqueio cadastrado para este mês.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Novo Bloqueio -->
<div id="bloqueio-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
    <div class="glass-panel max-w-md w-full rounded-3xl p-6 shadow-2xl border border-slate-700">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <h3 class="font-bold text-lg text-white flex items-center gap-2">
                <i data-lucide="ban" class="w-5 h-5 text-rose-400"></i>
                <span>Bloquear Data / Turno</span>
            </h3>
            <button onclick="closeBloqueioModal()" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.bloquear') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <div>
                <label for="bl-data" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Data</label>
                <input type="date" id="bl-data" name="data" required min="{{ date('Y-m-d') }}"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-rose-500">
            </div>

            <div>
                <label for="bl-turno" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Turno a Bloquear</label>
                <select id="bl-turno" name="turno" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-rose-500">
                    <option value="ambos">Ambos os Turnos (Dia Inteiro)</option>
                    <option value="manha">Somente Manhã</option>
                    <option value="tarde">Somente Tarde</option>
                </select>
            </div>

            <div>
                <label for="bl-motivo" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">Motivo do Bloqueio</label>
                <input type="text" id="bl-motivo" name="motivo" placeholder="Ex: Manutenção na galeria / Feriado institucional"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-rose-500">
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeBloqueioModal()" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-800 text-slate-300 hover:text-white">Cancelar</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-900/40">Salvar Bloqueio</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openBloqueioModal() {
        document.getElementById('bloqueio-modal').classList.remove('hidden');
    }
    function closeBloqueioModal() {
        document.getElementById('bloqueio-modal').classList.add('hidden');
    }
</script>
@endpush
