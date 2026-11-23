@extends('layouts.app')

@section('title', 'Agendamento de Visitas - CENPALEO')

@section('content')
<section class="relative overflow-hidden bg-white border-b border-slate-100">
    <div class="absolute inset-y-0 right-0 w-1/2 bg-gradient-to-br from-cyan-50 via-white to-orange-50 hidden lg:block"></div>
    <div class="absolute -left-20 top-16 w-52 h-52 rounded-full border-[32px] border-cenpaleo-cyan/10"></div>
    <div class="absolute right-[38%] bottom-10 w-14 h-14 bg-cenpaleo-purple/10 rotate-45 rounded-lg"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 grid lg:grid-cols-2 gap-10 items-center relative">
        <div class="max-w-xl">
            <p class="section-kicker">Museu da Terra e da Vida</p>
            <h1 class="mt-3 text-4xl sm:text-5xl lg:text-6xl leading-[1.06] font-extrabold text-cenpaleo-navy tracking-tight">
                Agende sua visita ao <span class="text-cenpaleo-purple">CENPALEO</span>
            </h1>
            <div class="brand-rule mt-6"></div>
            <p class="mt-6 text-sm sm:text-base leading-7 text-slate-600">
                Escolha uma data disponível e envie os dados da sua escola, instituição ou grupo. A equipe do museu receberá a solicitação já organizada por dia e turno.
            </p>
            <div class="mt-7 flex flex-wrap gap-3 text-xs font-semibold text-cenpaleo-navy">
                <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-cyan-50 border border-cyan-100"><i data-lucide="calendar-check-2" class="w-4 h-4 text-cenpaleo-cyan-dark"></i>Consulta em tempo real</span>
                <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-orange-50 border border-orange-100"><i data-lucide="users" class="w-4 h-4 text-cenpaleo-orange"></i>Escolas e visitantes</span>
            </div>
        </div>

        <div class="relative min-h-[320px] sm:min-h-[390px] flex items-center justify-center">
            <div class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-cenpaleo-cyan/10"></div>
            <div class="absolute top-8 right-6 sm:right-12 w-20 h-20 bg-cenpaleo-orange/15 rounded-full"></div>
            <div class="absolute bottom-8 left-4 sm:left-12 w-16 h-16 bg-cenpaleo-purple/15 rotate-12"></div>
            <img src="{{ asset('images/cenpaleo/livro-descoberta.png') }}" alt="Livro aberto com a descoberta do dinossauro Berthasaura leopoldinae" class="relative z-10 w-full max-w-[520px] drop-shadow-2xl">
        </div>
    </div>
</section>

<section id="agenda" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-7">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5">
        <div>
            <p class="section-kicker">Planeje sua experiência</p>
            <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-cenpaleo-navy">Calendário de visitas</h2>
            <p class="mt-2 text-sm text-slate-500 max-w-2xl">Selecione um dia com horário livre. Turnos agendados ou bloqueados ficam indisponíveis imediatamente.</p>
        </div>

        <div class="museum-card rounded-2xl px-4 py-3 flex flex-wrap gap-x-5 gap-y-2 text-[11px] font-semibold">
            <span class="flex items-center gap-2"><i class="w-2.5 h-2.5 rounded-full bg-cenpaleo-teal"></i>Disponível</span>
            <span class="flex items-center gap-2"><i class="w-2.5 h-2.5 rounded-full bg-cenpaleo-orange"></i>Agendado</span>
            <span class="flex items-center gap-2"><i class="w-2.5 h-2.5 rounded-full bg-rose-500"></i>Bloqueado</span>
        </div>
    </div>

    <div class="museum-card rounded-3xl overflow-hidden">
        <div class="px-5 sm:px-8 py-5 bg-cenpaleo-navy text-white flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <button id="btn-prev-month" type="button" class="p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors" aria-label="Mês anterior"><i data-lucide="chevron-left" class="w-5 h-5"></i></button>
                <h3 id="current-month-label" class="text-base sm:text-xl font-bold min-w-[210px] text-center">Carregando...</h3>
                <button id="btn-next-month" type="button" class="p-2.5 rounded-full bg-white/10 hover:bg-white/20 transition-colors" aria-label="Próximo mês"><i data-lucide="chevron-right" class="w-5 h-5"></i></button>
            </div>
            <div class="flex items-center gap-3">
                <span id="last-updated-label" class="hidden lg:inline text-[10px] text-white/60"></span>
                <button id="btn-today" type="button" class="px-4 py-2 rounded-full bg-cenpaleo-orange hover:bg-orange-500 text-white text-xs font-bold transition-colors">Ir para hoje</button>
            </div>
        </div>

        <div class="p-4 sm:p-7 overflow-x-auto">
            <div class="min-w-[760px]">
                <div class="grid grid-cols-7 gap-2 mb-3 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <div>Dom</div><div>Seg</div><div>Ter</div><div>Qua</div><div>Qui</div><div>Sex</div><div>Sáb</div>
                </div>
                <div id="calendar-grid" class="grid grid-cols-7 gap-2 min-h-[430px]">
                    <div class="col-span-7 py-24 flex flex-col items-center justify-center text-slate-400">
                        <div class="w-8 h-8 border-2 border-cenpaleo-cyan border-t-transparent rounded-full animate-spin mb-3"></div>
                        <span class="text-xs">Carregando disponibilidade...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
        <article class="museum-card rounded-2xl p-5 flex gap-4"><span class="w-11 h-11 rounded-full bg-cyan-50 text-cenpaleo-cyan-dark flex items-center justify-center shrink-0"><i data-lucide="mouse-pointer-click" class="w-5 h-5"></i></span><div><h3 class="text-sm font-bold text-cenpaleo-navy">1. Escolha a data</h3><p class="mt-1 text-xs leading-5 text-slate-500">Clique em um dia que ainda tenha ao menos um turno livre.</p></div></article>
        <article class="museum-card rounded-2xl p-5 flex gap-4"><span class="w-11 h-11 rounded-full bg-orange-50 text-cenpaleo-orange flex items-center justify-center shrink-0"><i data-lucide="clipboard-pen-line" class="w-5 h-5"></i></span><div><h3 class="text-sm font-bold text-cenpaleo-navy">2. Informe os dados</h3><p class="mt-1 text-xs leading-5 text-slate-500">Selecione manhã, tarde ou os dois turnos e identifique o grupo.</p></div></article>
        <article class="museum-card rounded-2xl p-5 flex gap-4"><span class="w-11 h-11 rounded-full bg-violet-50 text-cenpaleo-purple flex items-center justify-center shrink-0"><i data-lucide="mail-check" class="w-5 h-5"></i></span><div><h3 class="text-sm font-bold text-cenpaleo-navy">3. Aguarde a confirmação</h3><p class="mt-1 text-xs leading-5 text-slate-500">A equipe do CENPALEO analisará a solicitação e entrará em contato.</p></div></article>
    </div>
</section>

<div id="booking-modal" class="fixed inset-0 z-50 hidden modal-backdrop items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="booking-title">
    <div class="museum-card max-w-2xl w-full rounded-3xl p-5 sm:p-8 my-6">
        <div class="flex items-start justify-between pb-5 border-b border-slate-100">
            <div class="flex items-start gap-3">
                <span class="w-11 h-11 rounded-full bg-cyan-50 text-cenpaleo-cyan-dark flex items-center justify-center shrink-0"><i data-lucide="calendar-plus" class="w-5 h-5"></i></span>
                <div><h2 id="booking-title" class="text-xl font-bold text-cenpaleo-navy">Solicitar agendamento</h2><p id="modal-date-display" class="text-xs font-semibold text-cenpaleo-cyan-dark mt-1">Data selecionada</p></div>
            </div>
            <button type="button" onclick="closeBookingModal()" class="p-2 rounded-full text-slate-400 hover:bg-slate-100 hover:text-cenpaleo-navy" aria-label="Fechar"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <div id="modal-alert" class="hidden mt-5 p-4 rounded-2xl text-sm font-medium"></div>

        <form id="booking-form" class="mt-6 space-y-5">
            <input type="hidden" id="input-data" name="data">

            <fieldset>
                <legend class="text-[11px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-3">Selecione o turno <span class="text-rose-500">*</span></legend>
                <div class="grid sm:grid-cols-2 gap-3" id="turnos-options">
                    <label id="option-manha" class="p-4 rounded-2xl border border-slate-200 flex items-center justify-between cursor-pointer hover:border-cenpaleo-cyan transition-colors bg-white">
                        <div class="flex items-center gap-3"><input type="radio" name="turno" value="manha" id="radio-manha" required class="w-4 h-4 accent-cenpaleo-purple"><div><div class="text-sm font-bold text-cenpaleo-navy">Manhã</div><div class="text-[11px] text-slate-500">08:30 às 11:30</div></div></div>
                        <span id="badge-manha" class="text-[10px] font-bold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">Livre</span>
                    </label>
                    <label id="option-tarde" class="p-4 rounded-2xl border border-slate-200 flex items-center justify-between cursor-pointer hover:border-cenpaleo-cyan transition-colors bg-white">
                        <div class="flex items-center gap-3"><input type="radio" name="turno" value="tarde" id="radio-tarde" required class="w-4 h-4 accent-cenpaleo-purple"><div><div class="text-sm font-bold text-cenpaleo-navy">Tarde</div><div class="text-[11px] text-slate-500">13:30 às 16:30</div></div></div>
                        <span id="badge-tarde" class="text-[10px] font-bold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700">Livre</span>
                    </label>
                    <label id="option-ambos" class="sm:col-span-2 p-4 rounded-2xl border border-violet-200 flex items-center justify-between cursor-pointer hover:border-cenpaleo-purple transition-colors bg-violet-50/50">
                        <div class="flex items-center gap-3"><input type="radio" name="turno" value="ambos" id="radio-ambos" required class="w-4 h-4 accent-cenpaleo-purple"><div><div class="text-sm font-bold text-cenpaleo-navy">Manhã e Tarde</div><div class="text-[11px] text-slate-500">Reserve os dois turnos no mesmo pedido</div></div></div>
                        <span id="badge-ambos" class="text-[10px] font-bold px-2 py-1 rounded-full bg-violet-100 text-violet-700">Livre</span>
                    </label>
                </div>
            </fieldset>

            <div class="grid sm:grid-cols-2 gap-4">
                <div><label for="responsavel" class="block text-[11px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Nome do responsável *</label><input type="text" id="responsavel" name="responsavel" required placeholder="Ex.: Maria Silva" class="museum-field px-4 py-3 text-sm"></div>
                <div><label for="instituicao" class="block text-[11px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Instituição / Escola *</label><input type="text" id="instituicao" name="instituicao" required placeholder="Ex.: Escola Municipal" class="museum-field px-4 py-3 text-sm"></div>
                <div><label for="num_alunos" class="block text-[11px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Alunos / Visitantes *</label><input type="number" id="num_alunos" name="num_alunos" min="1" max="500" required placeholder="Ex.: 40" class="museum-field px-4 py-3 text-sm"></div>
                <div><label for="telefone" class="block text-[11px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Telefone / WhatsApp *</label><input type="tel" id="telefone" name="telefone" required placeholder="(00) 90000-0000" class="museum-field px-4 py-3 text-sm"></div>
            </div>
            <div><label for="email" class="block text-[11px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">E-mail para contato *</label><input type="email" id="email" name="email" required placeholder="contato@instituicao.com.br" class="museum-field px-4 py-3 text-sm"></div>

            <label class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 cursor-pointer">
                <input type="checkbox" id="lgpd" name="lgpd" value="1" required class="mt-0.5 w-4 h-4 accent-cenpaleo-purple">
                <span class="text-xs text-slate-600 leading-5">Concordo com o uso dos dados informados exclusivamente para organizar e comunicar esta visita, conforme a LGPD.</span>
            </label>

            <div class="pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <button type="button" onclick="closeBookingModal()" class="px-5 py-3 text-xs font-bold rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50">Cancelar</button>
                <button type="submit" id="btn-submit-booking" class="px-6 py-3 text-xs font-bold rounded-full bg-cenpaleo-purple hover:bg-violet-700 disabled:opacity-60 text-white shadow-lg shadow-violet-200 transition-colors flex items-center justify-center gap-2">
                    <span id="btn-submit-text">Enviar solicitação</span><span id="btn-submit-spinner" class="hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentMonthDate = new Date();
    currentMonthDate.setDate(1);
    let monthDataCache = {};
    let lastRefreshAt = 0;

    const monthNames = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

    document.addEventListener('DOMContentLoaded', () => {
        loadMonthAvailability(currentMonthDate);
        document.getElementById('btn-prev-month').addEventListener('click', () => changeMonth(-1));
        document.getElementById('btn-next-month').addEventListener('click', () => changeMonth(1));
        document.getElementById('btn-today').addEventListener('click', () => {
            currentMonthDate = new Date();
            currentMonthDate.setDate(1);
            loadMonthAvailability(currentMonthDate);
        });
        document.getElementById('booking-form').addEventListener('submit', handleFormSubmit);

        window.setInterval(() => {
            if (!document.hidden && document.getElementById('booking-modal').classList.contains('hidden')) {
                loadMonthAvailability(currentMonthDate, false);
            }
        }, 30000);

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && Date.now() - lastRefreshAt > 5000) {
                loadMonthAvailability(currentMonthDate, false);
            }
        });
    });

    function changeMonth(offset) {
        currentMonthDate.setDate(1);
        currentMonthDate.setMonth(currentMonthDate.getMonth() + offset);
        loadMonthAvailability(currentMonthDate);
    }

    function localDateKey(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    async function loadMonthAvailability(dateObj, showLoading = true) {
        const year = dateObj.getFullYear();
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const mesParam = `${year}-${month}`;
        const grid = document.getElementById('calendar-grid');

        document.getElementById('current-month-label').innerText = `${monthNames[dateObj.getMonth()]} de ${year}`;
        if (showLoading) {
            grid.innerHTML = '<div class="col-span-7 py-24 flex flex-col items-center justify-center text-slate-400"><div class="w-8 h-8 border-2 border-cenpaleo-cyan border-t-transparent rounded-full animate-spin mb-3"></div><span class="text-xs">Atualizando horários...</span></div>';
        }

        try {
            const response = await fetch(`/api/disponibilidade?mes=${mesParam}&_=${Date.now()}`, {
                cache: 'no-store',
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) throw new Error('Falha ao consultar a disponibilidade.');
            const data = await response.json();
            monthDataCache = data.dias || {};
            lastRefreshAt = Date.now();
            renderCalendarGrid(dateObj, monthDataCache);
            document.getElementById('last-updated-label').innerText = `Atualizado às ${new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
        } catch (error) {
            console.error('Erro ao carregar disponibilidade:', error);
            if (showLoading || Object.keys(monthDataCache).length === 0) {
                grid.innerHTML = '<div class="col-span-7 py-16 text-center text-rose-600 text-sm font-semibold">Não foi possível atualizar o calendário. Tente novamente em instantes.</div>';
            }
        }
    }

    function renderCalendarGrid(dateObj, daysData) {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';
        const year = dateObj.getFullYear();
        const month = dateObj.getMonth();
        const firstDayIndex = new Date(year, month, 1).getDay();
        const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
        const todayStr = localDateKey(new Date());

        for (let index = 0; index < firstDayIndex; index++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = 'h-28 rounded-2xl bg-slate-50 border border-slate-100';
            grid.appendChild(emptyCell);
        }

        for (let day = 1; day <= totalDaysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const info = daysData[dateStr] || { status_dia: 'vermelho', tem_disponibilidade: false, is_weekend: false, turnos: { manha: { status: 'bloqueado' }, tarde: { status: 'bloqueado' } } };
            const dayCard = document.createElement('button');
            dayCard.type = 'button';
            const isPastDate = dateStr < todayStr;
            const isToday = dateStr === todayStr;
            const canSchedule = !isPastDate && info.tem_disponibilidade;

            let colorClasses = 'bg-slate-50 border-slate-200 text-slate-400';
            let badgeText = isPastDate ? 'Passado' : 'Indisponível';
            let badgeClasses = 'bg-slate-200 text-slate-500';

            if (!isPastDate && info.is_weekend) {
                colorClasses = 'bg-slate-50 border-slate-200 text-slate-400';
                badgeText = 'Fechado';
            } else if (canSchedule) {
                colorClasses = 'bg-emerald-50/60 border-emerald-200 hover:border-cenpaleo-teal hover:shadow-md cursor-pointer text-cenpaleo-navy';
                badgeText = 'Disponível';
                badgeClasses = 'bg-emerald-100 text-emerald-700';
            } else if (!isPastDate && info.status_dia === 'amarelo') {
                colorClasses = 'bg-orange-50/70 border-orange-200 text-cenpaleo-navy';
                badgeText = 'Ocupado';
                badgeClasses = 'bg-orange-100 text-orange-700';
            } else if (!isPastDate) {
                colorClasses = 'bg-rose-50/60 border-rose-200 text-cenpaleo-navy';
                badgeClasses = 'bg-rose-100 text-rose-700';
            }

            dayCard.disabled = !canSchedule;
            dayCard.className = `h-28 rounded-2xl p-2.5 border text-left flex flex-col justify-between transition-all ${colorClasses}`;
            dayCard.innerHTML = `
                <div class="flex items-center justify-between gap-1">
                    <span class="text-sm font-extrabold ${isToday ? 'w-7 h-7 rounded-full bg-cenpaleo-purple text-white flex items-center justify-center' : ''}">${day}</span>
                    <span class="text-[9px] font-bold px-1.5 py-1 rounded-full ${badgeClasses}">${badgeText}</span>
                </div>
                <div class="space-y-1.5 text-[10px]">
                    <div class="flex items-center justify-between gap-1"><span>Manhã</span><span class="font-bold ${getTurnoTextColor(info.turnos?.manha?.status)}">${formatTurnoText(info.turnos?.manha?.status)}</span></div>
                    <div class="flex items-center justify-between gap-1"><span>Tarde</span><span class="font-bold ${getTurnoTextColor(info.turnos?.tarde?.status)}">${formatTurnoText(info.turnos?.tarde?.status)}</span></div>
                </div>`;

            if (canSchedule) dayCard.addEventListener('click', () => openBookingModal(dateStr, info));
            grid.appendChild(dayCard);
        }
    }

    function getTurnoTextColor(status) {
        if (status === 'livre') return 'text-emerald-700';
        if (status === 'agendado') return 'text-orange-700';
        return 'text-rose-600';
    }

    function formatTurnoText(status) {
        if (status === 'livre') return 'Livre';
        if (status === 'agendado') return 'Agendado';
        return 'Bloqueado';
    }

    function configureTurnOption(name, available, statusText) {
        const radio = document.getElementById(`radio-${name}`);
        const badge = document.getElementById(`badge-${name}`);
        const option = document.getElementById(`option-${name}`);
        radio.disabled = !available;
        radio.checked = false;
        badge.innerText = available ? 'Livre' : statusText;
        badge.className = available ? 'text-[10px] font-bold px-2 py-1 rounded-full bg-emerald-50 text-emerald-700' : 'text-[10px] font-bold px-2 py-1 rounded-full bg-rose-50 text-rose-600';
        option.classList.toggle('opacity-50', !available);
        option.classList.toggle('cursor-not-allowed', !available);
    }

    function openBookingModal(dateStr, dayInfo) {
        const form = document.getElementById('booking-form');
        form.reset();
        document.getElementById('input-data').value = dateStr;
        document.getElementById('modal-alert').classList.add('hidden');
        const [year, month, day] = dateStr.split('-');
        document.getElementById('modal-date-display').innerText = `Visita em ${day}/${month}/${year}`;

        const morningStatus = dayInfo.turnos?.manha?.status;
        const afternoonStatus = dayInfo.turnos?.tarde?.status;
        const morningAvailable = morningStatus === 'livre';
        const afternoonAvailable = afternoonStatus === 'livre';
        configureTurnOption('manha', morningAvailable, morningStatus === 'agendado' ? 'Agendado' : 'Bloqueado');
        configureTurnOption('tarde', afternoonAvailable, afternoonStatus === 'agendado' ? 'Agendado' : 'Bloqueado');
        configureTurnOption('ambos', morningAvailable && afternoonAvailable, 'Indisponível');

        if (morningAvailable) document.getElementById('radio-manha').checked = true;
        else if (afternoonAvailable) document.getElementById('radio-tarde').checked = true;

        const modal = document.getElementById('booking-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeBookingModal() {
        const modal = document.getElementById('booking-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function handleFormSubmit(event) {
        event.preventDefault();
        const alertElement = document.getElementById('modal-alert');
        const buttonText = document.getElementById('btn-submit-text');
        const buttonSpinner = document.getElementById('btn-submit-spinner');
        const submitButton = document.getElementById('btn-submit-booking');
        alertElement.classList.add('hidden');
        submitButton.disabled = true;
        buttonText.innerText = 'Enviando...';
        buttonSpinner.classList.remove('hidden');

        try {
            const response = await fetch('/api/agendar', {
                method: 'POST',
                cache: 'no-store',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(Object.fromEntries(new FormData(event.target).entries())),
            });
            const result = await response.json();

            if (response.ok && result.success) {
                alertElement.className = 'mt-5 p-4 rounded-2xl text-sm font-semibold bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-2';
                alertElement.innerHTML = `<i data-lucide="check-circle-2" class="w-5 h-5 shrink-0"></i><span>${result.message}</span>`;
                alertElement.classList.remove('hidden');
                lucide.createIcons();
                await loadMonthAvailability(currentMonthDate, false);
                window.setTimeout(closeBookingModal, 1600);
            } else {
                const errorMessage = result.errors ? Object.values(result.errors).flat().join('<br>') : (result.message || 'Não foi possível processar o agendamento.');
                alertElement.className = 'mt-5 p-4 rounded-2xl text-sm font-semibold bg-rose-50 border border-rose-200 text-rose-700 flex items-start gap-2';
                alertElement.innerHTML = `<i data-lucide="triangle-alert" class="w-5 h-5 shrink-0"></i><div>${errorMessage}</div>`;
                alertElement.classList.remove('hidden');
                lucide.createIcons();
                await loadMonthAvailability(currentMonthDate, false);
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            alertElement.className = 'mt-5 p-4 rounded-2xl text-sm font-semibold bg-rose-50 border border-rose-200 text-rose-700';
            alertElement.innerText = 'Erro de conexão. Verifique sua rede e tente novamente.';
            alertElement.classList.remove('hidden');
        } finally {
            submitButton.disabled = false;
            buttonText.innerText = 'Enviar solicitação';
            buttonSpinner.classList.add('hidden');
        }
    }
</script>
@endpush
