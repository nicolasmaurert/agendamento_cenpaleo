@extends('layouts.app')

@section('title', 'Painel Administrativo - CENPALEO')

@section('content')
<section class="bg-cenpaleo-navy text-white relative overflow-hidden">
    <div class="absolute -right-12 -top-20 w-72 h-72 rounded-full border-[48px] border-white/5"></div>
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 py-9 relative flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <p class="text-cenpaleo-cyan text-[11px] uppercase tracking-[.18em] font-bold">Área administrativa</p>
            <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold">Gestão de visitas</h1>
            <p class="mt-2 text-sm text-white/65">Visualize a agenda do museu, confirme solicitações e bloqueie turnos.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button type="button" onclick="openBloqueioModal()" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-cenpaleo-orange hover:bg-orange-500 text-white text-xs font-bold transition-colors shadow-lg shadow-black/10"><i data-lucide="calendar-x-2" class="w-4 h-4"></i>Bloquear data / turno</button>
            <form action="{{ route('admin.logout') }}" method="POST">@csrf<button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors"><i data-lucide="log-out" class="w-4 h-4"></i>Sair</button></form>
        </div>
    </div>
</section>

<div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 py-9 space-y-7">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-3"><i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('warning'))
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-sm font-semibold flex items-center gap-3"><i data-lucide="mail-warning" class="w-5 h-5 shrink-0"></i><span>{{ session('warning') }}</span></div>
    @endif
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold flex items-start gap-3"><i data-lucide="triangle-alert" class="w-5 h-5 shrink-0 mt-0.5"></i><div>{{ $errors->first() }}</div></div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
        <article class="museum-card rounded-2xl p-4"><p class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Total no mês</p><p class="mt-1 text-2xl font-extrabold text-cenpaleo-navy">{{ $stats['total'] }}</p></article>
        <article class="museum-card rounded-2xl p-4 border-t-4 border-t-cenpaleo-orange"><p class="text-[10px] uppercase tracking-wider font-bold text-orange-600">Pendentes</p><p class="mt-1 text-2xl font-extrabold text-cenpaleo-navy">{{ $stats['pendentes'] }}</p></article>
        <article class="museum-card rounded-2xl p-4 border-t-4 border-t-cenpaleo-teal"><p class="text-[10px] uppercase tracking-wider font-bold text-emerald-700">Confirmados</p><p class="mt-1 text-2xl font-extrabold text-cenpaleo-navy">{{ $stats['confirmados'] }}</p></article>
        <article class="museum-card rounded-2xl p-4 border-t-4 border-t-rose-500"><p class="text-[10px] uppercase tracking-wider font-bold text-rose-600">Cancelados</p><p class="mt-1 text-2xl font-extrabold text-cenpaleo-navy">{{ $stats['cancelados'] }}</p></article>
        <article class="museum-card rounded-2xl p-4 col-span-2 md:col-span-1 border-t-4 border-t-cenpaleo-purple"><p class="text-[10px] uppercase tracking-wider font-bold text-violet-700">Bloqueios</p><p class="mt-1 text-2xl font-extrabold text-cenpaleo-navy">{{ $stats['bloqueios'] }}</p></article>
    </div>

    <div class="grid 2xl:grid-cols-[minmax(0,1.8fr)_minmax(420px,0.8fr)] gap-7 items-start">
    <section class="museum-card rounded-3xl overflow-hidden min-w-0" aria-labelledby="admin-calendar-title">
        <div class="bg-white px-5 sm:px-7 py-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <p class="section-kicker">Agenda mensal</p>
                <h2 id="admin-calendar-title" class="mt-1 text-xl sm:text-2xl font-extrabold text-cenpaleo-navy">Quem visitará o CENPALEO</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard', ['mes' => $mesAnterior, 'status' => $status]) }}" class="p-2.5 rounded-full border border-slate-200 hover:border-cenpaleo-cyan hover:text-cenpaleo-cyan-dark transition-colors" aria-label="Mês anterior"><i data-lucide="chevron-left" class="w-5 h-5"></i></a>
                <span class="min-w-[190px] text-center text-sm font-bold text-cenpaleo-navy">{{ $mesLabel }}</span>
                <a href="{{ route('admin.dashboard', ['mes' => $proximoMes, 'status' => $status]) }}" class="p-2.5 rounded-full border border-slate-200 hover:border-cenpaleo-cyan hover:text-cenpaleo-cyan-dark transition-colors" aria-label="Próximo mês"><i data-lucide="chevron-right" class="w-5 h-5"></i></a>
            </div>
        </div>

        <div class="p-4 sm:p-6 overflow-x-auto">
            <div class="min-w-[900px]">
                <div class="grid grid-cols-7 gap-2 mb-2 text-center text-[10px] uppercase tracking-wider font-bold text-slate-400">
                    <div>Domingo</div><div>Segunda</div><div>Terça</div><div>Quarta</div><div>Quinta</div><div>Sexta</div><div>Sábado</div>
                </div>
                <div class="grid grid-cols-7 gap-2">
                    @for($vazio = 0; $vazio < $primeiroDiaSemana; $vazio++)
                        <div class="min-h-44 rounded-2xl bg-slate-50 border border-slate-100"></div>
                    @endfor

                    @foreach($diasCalendario as $dia)
                        @php
                            $manha = $dia['turnos']['manha'];
                            $tarde = $dia['turnos']['tarde'];
                            $temVisita = $manha['agendamento'] || $tarde['agendamento'];
                            $temBloqueio = $manha['bloqueio'] || $tarde['bloqueio'];
                        @endphp
                        <article class="min-h-44 rounded-2xl border p-2.5 flex flex-col {{ $dia['fim_de_semana'] ? 'bg-slate-50 border-slate-200' : ($temVisita ? 'bg-orange-50/50 border-orange-200' : ($temBloqueio ? 'bg-rose-50/50 border-rose-200' : 'bg-emerald-50/40 border-emerald-200')) }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-extrabold {{ $dia['data'] === now()->format('Y-m-d') ? 'bg-cenpaleo-purple text-white' : 'text-cenpaleo-navy bg-white border border-slate-100' }}">{{ $dia['dia'] }}</span>
                                @if($dia['fim_de_semana'])<span class="text-[8px] uppercase font-bold text-slate-400">Fechado</span>@endif
                            </div>

                            <div class="space-y-2 flex-grow">
                                @foreach(['manha' => '08:30–11:30', 'tarde' => '13:30–16:30'] as $turno => $horario)
                                    @php($item = $dia['turnos'][$turno])
                                    <div class="rounded-xl p-2 bg-white/90 border border-white shadow-sm min-h-[58px]">
                                        <div class="flex items-center justify-between gap-1 mb-1">
                                            <span class="text-[9px] uppercase tracking-wide font-bold text-slate-400">{{ $turno === 'manha' ? 'Manhã' : 'Tarde' }}</span>
                                            <span class="text-[8px] font-semibold text-slate-400">{{ $horario }}</span>
                                        </div>
                                        @if($dia['fim_de_semana'])
                                            <p class="text-[9px] text-slate-400">Sem expediente</p>
                                        @elseif($item['bloqueio'])
                                            <p class="text-[10px] font-bold text-rose-600 flex items-center gap-1"><i data-lucide="ban" class="w-3 h-3"></i>Bloqueado</p>
                                            <p class="text-[8px] leading-3 text-slate-500 truncate" title="{{ $item['bloqueio']->motivo }}">{{ $item['bloqueio']->motivo ?: 'Uso interno' }}</p>
                                        @elseif($item['agendamento'])
                                            <p class="text-[10px] font-bold leading-4 text-cenpaleo-navy truncate" title="{{ $item['agendamento']->responsavel }}">{{ $item['agendamento']->responsavel }}</p>
                                            <p class="text-[8px] leading-3 text-slate-500 truncate" title="{{ $item['agendamento']->instituicao }}">{{ $item['agendamento']->instituicao }}</p>
                                            <span class="mt-1 inline-flex text-[8px] font-bold {{ $item['agendamento']->status === 'confirmado' ? 'text-emerald-700' : 'text-orange-700' }}">{{ ucfirst($item['agendamento']->status) }} · {{ $item['agendamento']->num_alunos }} visitantes</span>
                                        @else
                                            <p class="text-[10px] font-bold text-emerald-700 flex items-center gap-1"><i data-lucide="circle-check" class="w-3 h-3"></i>Livre</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="museum-card rounded-3xl p-5 sm:p-6 min-w-0 2xl:sticky 2xl:top-28" aria-labelledby="blocked-shifts-title">
        <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <p class="section-kicker">Indisponibilidades</p>
                <h2 id="blocked-shifts-title" class="mt-1 text-lg font-extrabold text-cenpaleo-navy">Turnos bloqueados</h2>
            </div>
            <span class="text-[10px] font-bold text-slate-400">{{ $bloqueios->count() }} registro(s)</span>
        </div>
        <div class="mt-5 space-y-3">
            @forelse($bloqueios as $bloqueio)
                <article class="p-4 rounded-2xl bg-rose-50/60 border border-rose-100 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-cenpaleo-navy text-sm">{{ $bloqueio->data->format('d/m/Y') }}</p>
                        <p class="text-[10px] text-rose-600 font-bold uppercase">{{ $bloqueio->turno === 'ambos' ? 'Manhã e Tarde' : ucfirst($bloqueio->turno) }}</p>
                        <p class="mt-1 text-[10px] text-slate-500">{{ $bloqueio->motivo ?: 'Não especificado' }}</p>
                    </div>
                    <form action="{{ route('admin.desbloquear', $bloqueio->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" title="Remover bloqueio" onclick="openConfirmationModal(this)" data-confirm-title="Remover bloqueio" data-confirm-message="Liberar {{ $bloqueio->turno === 'ambos' ? 'os turnos da manhã e da tarde' : 'o turno da '.($bloqueio->turno === 'manha' ? 'manhã' : 'tarde') }} em {{ $bloqueio->data->format('d/m/Y') }}? O período voltará a aparecer disponível para agendamento." data-confirm-label="Sim, remover" class="p-2.5 rounded-full bg-white text-rose-500 hover:bg-rose-100"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </form>
                </article>
            @empty
                <p class="py-7 text-center text-xs text-slate-400">Nenhum bloqueio cadastrado neste mês.</p>
            @endforelse
        </div>
    </section>
    </div>

    <section class="museum-card rounded-3xl overflow-hidden min-w-0" aria-labelledby="requests-title">
        <div class="px-5 sm:px-7 py-5 border-b border-slate-100 flex flex-col gap-4">
            <div><p class="section-kicker">Solicitações</p><h2 id="requests-title" class="mt-1 text-xl font-extrabold text-cenpaleo-navy">Lista de agendamentos</h2></div>
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2"><input type="hidden" name="status" value="{{ $status }}"><label for="mes-select" class="text-[10px] uppercase font-bold text-slate-400">Mês</label><input type="month" id="mes-select" name="mes" value="{{ $mes }}" onchange="this.form.submit()" class="museum-field px-3 py-2 text-xs"></form>
                <nav class="flex items-center gap-1 p-1 bg-slate-100 rounded-xl overflow-x-auto">
                    @foreach(['todos' => 'Todos', 'pendente' => 'Pendentes', 'confirmado' => 'Confirmados', 'cancelado' => 'Cancelados'] as $key => $label)
                        <a href="{{ route('admin.dashboard', ['mes' => $mes, 'status' => $key]) }}" class="px-3 py-1.5 rounded-lg text-[10px] font-bold whitespace-nowrap {{ $status === $key ? 'bg-white text-cenpaleo-purple shadow-sm' : 'text-slate-500 hover:text-cenpaleo-navy' }}">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[940px] text-left text-xs">
                <thead class="bg-slate-50 text-slate-400 uppercase tracking-wider text-[9px]"><tr><th class="py-3 px-5">Data e turno</th><th class="py-3 px-5">Responsável / instituição</th><th class="py-3 px-5">Alunos / visitantes</th><th class="py-3 px-5">Contato</th><th class="py-3 px-5">Status</th><th class="py-3 px-5 text-right">Ações</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @forelse($agendamentos as $agendamento)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-4 px-5"><p class="font-bold text-cenpaleo-navy">{{ $agendamento->data->format('d/m/Y') }}</p><span class="text-[9px] font-bold uppercase text-cenpaleo-purple">{{ $agendamento->turno === 'ambos' ? 'Manhã e Tarde' : ucfirst($agendamento->turno) }}</span></td>
                            <td class="py-4 px-5"><p class="font-bold text-cenpaleo-navy">{{ $agendamento->responsavel }}</p><p class="text-[10px] text-slate-400">{{ $agendamento->instituicao }}</p></td>
                            <td class="py-4 px-5"><span class="font-extrabold text-cenpaleo-teal">{{ $agendamento->num_alunos }}</span></td>
                            <td class="py-4 px-5"><p class="flex items-center gap-1.5"><i data-lucide="phone" class="w-3 h-3"></i>{{ $agendamento->telefone }}</p><p class="mt-1 flex items-center gap-1.5 text-[10px] text-slate-400"><i data-lucide="mail" class="w-3 h-3"></i>{{ $agendamento->email }}</p></td>
                            <td class="py-4 px-5"><span class="px-2.5 py-1 rounded-full text-[9px] font-bold {{ $agendamento->status === 'confirmado' ? 'bg-emerald-100 text-emerald-700' : ($agendamento->status === 'pendente' ? 'bg-orange-100 text-orange-700' : 'bg-rose-100 text-rose-700') }}">{{ ucfirst($agendamento->status) }}</span></td>
                            <td class="py-4 px-5"><div class="flex items-center justify-end gap-2">
                                @if($agendamento->status !== 'confirmado')<form action="{{ route('admin.agendamentos.update', $agendamento->id) }}" method="POST">@csrf @method('PUT')<input type="hidden" name="status" value="confirmado"><button type="submit" title="Confirmar e enviar e-mail" class="p-2 rounded-full bg-emerald-50 text-emerald-700 hover:bg-emerald-100"><i data-lucide="check" class="w-4 h-4"></i></button></form>@endif
                                @if($agendamento->status !== 'cancelado')
                                    <form action="{{ route('admin.agendamentos.update', $agendamento->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="cancelado">
                                        <input type="hidden" name="motivo_cancelamento" class="js-confirmation-reason-input">
                                        <button type="button" title="{{ $agendamento->status === 'pendente' ? 'Negar solicitação' : 'Cancelar agendamento' }}" onclick="openConfirmationModal(this)" data-confirm-title="{{ $agendamento->status === 'pendente' ? 'Negar solicitação' : 'Cancelar agendamento' }}" data-confirm-message="{{ $agendamento->status === 'pendente' ? 'Informe por que a visita de '.$agendamento->responsavel.' não poderá ser aprovada.' : 'Informe por que a visita de '.$agendamento->responsavel.' precisará ser cancelada.' }} O motivo será enviado por e-mail ao cliente." data-confirm-label="{{ $agendamento->status === 'pendente' ? 'Negar e enviar e-mail' : 'Cancelar e enviar e-mail' }}" data-requires-reason="true" data-reason-label="{{ $agendamento->status === 'pendente' ? 'Motivo da negação' : 'Motivo do cancelamento' }}" data-reason-placeholder="Explique o motivo de forma clara para o cliente" class="p-2 rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100"><i data-lucide="ban" class="w-4 h-4"></i></button>
                                    </form>
                                @endif
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-14 text-center text-slate-400"><i data-lucide="calendar-search" class="w-8 h-8 mx-auto mb-2"></i>Nenhum agendamento encontrado para este filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<div id="bloqueio-modal" class="fixed inset-0 z-50 hidden modal-backdrop items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="bloqueio-title">
    <div class="museum-card max-w-md w-full rounded-3xl p-6 sm:p-7">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100"><div class="flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center"><i data-lucide="calendar-x" class="w-5 h-5"></i></span><h2 id="bloqueio-title" class="font-bold text-lg text-cenpaleo-navy">Bloquear agenda</h2></div><button type="button" onclick="closeBloqueioModal()" class="p-2 rounded-full text-slate-400 hover:bg-slate-100"><i data-lucide="x" class="w-5 h-5"></i></button></div>
        @if($errors->has('bloqueio'))
            <div class="mt-5 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold leading-5 flex items-start gap-2"><i data-lucide="calendar-clock" class="w-4 h-4 shrink-0 mt-0.5"></i><span>{{ $errors->first('bloqueio') }}</span></div>
        @endif
        <form action="{{ route('admin.bloquear') }}" method="POST" class="mt-5 space-y-4">@csrf
            <div><label for="bl-data" class="block text-[10px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Data</label><input type="date" id="bl-data" name="data" required min="{{ now()->format('Y-m-d') }}" value="{{ old('data') }}" class="museum-field px-4 py-3 text-sm"></div>
            <div><label for="bl-turno" class="block text-[10px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Turno</label><select id="bl-turno" name="turno" required class="museum-field px-4 py-3 text-sm"><option value="ambos" @selected(old('turno') === 'ambos')>Manhã e Tarde (dia inteiro)</option><option value="manha" @selected(old('turno') === 'manha')>Somente manhã</option><option value="tarde" @selected(old('turno') === 'tarde')>Somente tarde</option></select></div>
            <div><label for="bl-motivo" class="block text-[10px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Motivo</label><input type="text" id="bl-motivo" name="motivo" value="{{ old('motivo') }}" placeholder="Ex.: manutenção, feriado ou evento" class="museum-field px-4 py-3 text-sm"></div>
            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3"><button type="button" onclick="closeBloqueioModal()" class="px-4 py-2.5 rounded-full text-xs font-bold border border-slate-200 text-slate-500">Cancelar</button><button type="submit" class="px-5 py-2.5 rounded-full text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white">Salvar bloqueio</button></div>
        </form>
    </div>
</div>

<div id="confirmation-modal" class="fixed inset-0 z-[60] hidden modal-backdrop items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="confirmation-title" aria-describedby="confirmation-message">
    <div class="museum-card max-w-md w-full rounded-3xl p-6 sm:p-7" onclick="event.stopPropagation()">
        <div class="flex items-start gap-4">
            <span class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center shrink-0"><i data-lucide="triangle-alert" class="w-6 h-6"></i></span>
            <div class="min-w-0">
                <h2 id="confirmation-title" class="text-lg font-extrabold text-cenpaleo-navy">Confirmar ação</h2>
                <p id="confirmation-message" class="mt-2 text-sm leading-6 text-slate-600"></p>
            </div>
        </div>
        <div id="confirmation-reason-container" class="hidden mt-5">
            <label id="confirmation-reason-label" for="confirmation-reason" class="block text-[10px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Motivo</label>
            <textarea id="confirmation-reason" rows="4" maxlength="1000" class="museum-field px-4 py-3 text-sm resize-y" placeholder="Explique o motivo de forma clara para o cliente"></textarea>
            <div class="mt-1 flex items-start justify-between gap-3 text-[10px]">
                <p id="confirmation-reason-error" class="hidden text-rose-600 font-semibold">Informe um motivo com pelo menos 5 caracteres.</p>
                <p class="ml-auto text-slate-400"><span id="confirmation-reason-count">0</span>/1000</p>
            </div>
        </div>
        <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <button type="button" onclick="closeConfirmationModal()" class="px-5 py-2.5 rounded-full text-xs font-bold border border-slate-200 text-slate-500 hover:bg-slate-50">Voltar</button>
            <button type="button" id="confirmation-submit" onclick="submitConfirmedAction()" class="px-5 py-2.5 rounded-full text-xs font-bold bg-rose-600 hover:bg-rose-700 disabled:opacity-60 text-white flex items-center justify-center gap-2">
                <span id="confirmation-submit-label">Confirmar</span>
                <span id="confirmation-spinner" class="hidden w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let confirmationForm = null;

    function openBloqueioModal() {
        const modal = document.getElementById('bloqueio-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeBloqueioModal() {
        const modal = document.getElementById('bloqueio-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openConfirmationModal(button) {
        confirmationForm = button.closest('form');
        document.getElementById('confirmation-title').innerText = button.dataset.confirmTitle;
        document.getElementById('confirmation-message').innerText = button.dataset.confirmMessage;
        document.getElementById('confirmation-submit-label').innerText = button.dataset.confirmLabel;
        document.getElementById('confirmation-submit').disabled = false;
        document.getElementById('confirmation-spinner').classList.add('hidden');

        const reasonContainer = document.getElementById('confirmation-reason-container');
        const reasonInput = document.getElementById('confirmation-reason');
        const requiresReason = button.dataset.requiresReason === 'true';
        reasonContainer.classList.toggle('hidden', !requiresReason);
        reasonInput.required = requiresReason;
        reasonInput.value = '';
        reasonInput.placeholder = button.dataset.reasonPlaceholder || 'Explique o motivo de forma clara para o cliente';
        document.getElementById('confirmation-reason-label').innerText = button.dataset.reasonLabel || 'Motivo';
        document.getElementById('confirmation-reason-error').classList.add('hidden');
        document.getElementById('confirmation-reason-count').innerText = '0';

        const modal = document.getElementById('confirmation-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        (requiresReason ? reasonInput : document.getElementById('confirmation-submit')).focus();
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmation-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        confirmationForm = null;
    }

    function submitConfirmedAction() {
        if (!confirmationForm) return;

        const reasonInput = document.getElementById('confirmation-reason');
        if (reasonInput.required && reasonInput.value.trim().length < 5) {
            document.getElementById('confirmation-reason-error').classList.remove('hidden');
            reasonInput.focus();
            return;
        }

        const formReasonInput = confirmationForm.querySelector('.js-confirmation-reason-input');
        if (formReasonInput) formReasonInput.value = reasonInput.value.trim();

        const submitButton = document.getElementById('confirmation-submit');
        submitButton.disabled = true;
        document.getElementById('confirmation-spinner').classList.remove('hidden');
        confirmationForm.requestSubmit();
    }

    document.getElementById('confirmation-modal').addEventListener('click', closeConfirmationModal);
    document.getElementById('confirmation-reason').addEventListener('input', (event) => {
        document.getElementById('confirmation-reason-count').innerText = event.target.value.length;
        if (event.target.value.trim().length >= 5) document.getElementById('confirmation-reason-error').classList.add('hidden');
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !document.getElementById('confirmation-modal').classList.contains('hidden')) {
            closeConfirmationModal();
        }
    });

    @if($errors->has('bloqueio'))
        document.addEventListener('DOMContentLoaded', openBloqueioModal);
    @endif

    window.setInterval(() => {
        const modalClosed = document.getElementById('bloqueio-modal').classList.contains('hidden') && document.getElementById('confirmation-modal').classList.contains('hidden');
        const editing = ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement?.tagName);
        if (!document.hidden && modalClosed && !editing) window.location.reload();
    }, 60000);
</script>
@endpush
