@extends('layouts.app')

@section('title', 'Calendário de Visitas - Exposição Cenpaleo')

@section('content')
<div class="space-y-8">

    <!-- Hero / Intro Banner -->
    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 relative overflow-hidden shadow-2xl">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-semibold border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Agendamentos Abertos</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Reserve uma Visita para sua Escola ou Grupo</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Escolha uma data no calendário abaixo para agendar a visita da sua instituição à Exposição Paleontológica Cenpaleo. Cada turno atende 1 grupo por vez para garantir uma experiência completa.
                </p>
            </div>

            <!-- Legend Badge -->
            <div class="glass-card rounded-2xl p-4 border border-slate-700/80 w-full md:w-auto shrink-0 space-y-2">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Legenda do Calendário</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-1 gap-2 text-xs font-semibold">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50"></span>
                        <span class="text-slate-200">Verde: Disponível</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-amber-500 shadow-sm shadow-amber-500/50"></span>
                        <span class="text-slate-200">Amarelo: Agendado</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-rose-500 shadow-sm shadow-rose-500/50"></span>
                        <span class="text-slate-200">Vermelho: Indisponível</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container Card -->
    <div class="glass-panel rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl">
        
        <!-- Calendar Header Navigation -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <button id="btn-prev-month" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all flex items-center justify-center">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <h3 id="current-month-label" class="text-xl font-bold text-white tracking-tight min-w-[200px] text-center">
                    Carregando...
                </h3>
                <button id="btn-next-month" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all flex items-center justify-center">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <button id="btn-today" class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all">
                    Hoje
                </button>
            </div>
        </div>

        <!-- Days of Week Header -->
        <div class="grid grid-cols-7 gap-2 my-4 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">
            <div>Dom</div>
            <div>Seg</div>
            <div>Ter</div>
            <div>Qua</div>
            <div>Qui</div>
            <div>Sex</div>
            <div>Sáb</div>
        </div>

        <!-- Calendar Grid -->
        <div id="calendar-grid" class="grid grid-cols-7 gap-2 min-h-[360px]">
            <!-- Dynamic days will be rendered here -->
            <div class="col-span-7 py-20 flex flex-col items-center justify-center text-slate-500">
                <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                <span>Carregando disponibilidade do mês...</span>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div id="booking-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto">
    <div class="glass-panel max-w-xl w-full rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-700 relative my-8">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div>
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i data-lucide="calendar-plus" class="w-5 h-5 text-emerald-400"></i>
                    <span>Solicitar Agendamento</span>
                </h3>
                <p id="modal-date-display" class="text-xs font-semibold text-emerald-400 mt-1">Data selecionada</p>
            </div>
            <button onclick="closeBookingModal()" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Notification Alert Inside Modal -->
        <div id="modal-alert" class="hidden my-4 p-4 rounded-xl text-sm font-medium"></div>

        <!-- Form -->
        <form id="booking-form" class="mt-6 space-y-4">
            <input type="hidden" id="input-data" name="data">

            <!-- Turno Selection -->
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                    Selecione o Turno <span class="text-rose-400">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3" id="turnos-options">
                    <label id="option-manha" class="glass-card p-3 rounded-xl border border-slate-700 flex items-center justify-between cursor-pointer hover:border-emerald-500/50 transition-all">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="turno" value="manha" id="radio-manha" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 bg-slate-800 border-slate-600">
                            <div>
                                <div class="text-sm font-bold text-white">Manhã</div>
                                <div class="text-xs text-slate-400">08:30 às 11:30</div>
                            </div>
                        </div>
                        <span id="badge-manha" class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Livre</span>
                    </label>

                    <label id="option-tarde" class="glass-card p-3 rounded-xl border border-slate-700 flex items-center justify-between cursor-pointer hover:border-emerald-500/50 transition-all">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="turno" value="tarde" id="radio-tarde" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 bg-slate-800 border-slate-600">
                            <div>
                                <div class="text-sm font-bold text-white">Tarde</div>
                                <div class="text-xs text-slate-400">13:30 às 16:30</div>
                            </div>
                        </div>
                        <span id="badge-tarde" class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Livre</span>
                    </label>
                </div>
            </div>

            <!-- Form Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="responsavel" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Nome do Responsável <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="responsavel" name="responsavel" required placeholder="Ex: Profa. Maria Silva"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                </div>

                <div>
                    <label for="instituicao" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Instituição / Escola <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" id="instituicao" name="instituicao" required placeholder="Ex: Colégio Estadual Dom Pedro"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                </div>

                <div>
                    <label for="num_alunos" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Nº de Alunos <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" id="num_alunos" name="num_alunos" min="1" max="500" required placeholder="Ex: 40"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                </div>

                <div>
                    <label for="telefone" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                        Telefone / WhatsApp <span class="text-rose-400">*</span>
                    </label>
                    <input type="tel" id="telefone" name="telefone" required placeholder="(00) 90000-0000"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                </div>
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1">
                    E-mail para Confirmação <span class="text-rose-400">*</span>
                </label>
                <input type="email" id="email" name="email" required placeholder="seuemail@escola.com.br"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
            </div>

            <!-- LGPD Consent Checkbox -->
            <div class="pt-2">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="lgpd" name="lgpd" value="1" required class="mt-1 w-4 h-4 text-emerald-600 focus:ring-emerald-500 bg-slate-900 border-slate-700 rounded">
                    <span class="text-xs text-slate-300 leading-relaxed">
                        Declaro que li e concordo com o tratamento dos dados informados exclusivamente para fins de agendamento da visita guiada à Exposição Cenpaleo, em conformidade com a <strong>LGPD</strong>.
                    </span>
                </label>
            </div>

            <!-- Modal Footer Buttons -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeBookingModal()" class="px-5 py-2.5 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all">
                    Cancelar
                </button>
                <button type="submit" id="btn-submit-booking" class="px-6 py-2.5 text-xs font-bold rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-lg shadow-emerald-900/40 transition-all flex items-center gap-2">
                    <span id="btn-submit-text">Confirmar Agendamento</span>
                    <div id="btn-submit-spinner" class="hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentMonthDate = new Date();
    let monthDataCache = {};

    const monthNames = [
        "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
    ];

    document.addEventListener('DOMContentLoaded', () => {
        loadMonthAvailability(currentMonthDate);

        document.getElementById('btn-prev-month').addEventListener('click', () => {
            currentMonthDate.setMonth(currentMonthDate.getMonth() - 1);
            loadMonthAvailability(currentMonthDate);
        });

        document.getElementById('btn-next-month').addEventListener('click', () => {
            currentMonthDate.setMonth(currentMonthDate.getMonth() + 1);
            loadMonthAvailability(currentMonthDate);
        });

        document.getElementById('btn-today').addEventListener('click', () => {
            currentMonthDate = new Date();
            loadMonthAvailability(currentMonthDate);
        });

        document.getElementById('booking-form').addEventListener('submit', handleFormSubmit);
    });

    async function loadMonthAvailability(dateObj) {
        const year = dateObj.getFullYear();
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const mesParam = `${year}-${month}`;

        document.getElementById('current-month-label').innerText = `${monthNames[dateObj.getMonth()]} de ${year}`;
        const grid = document.getElementById('calendar-grid');
        
        grid.innerHTML = `
            <div class="col-span-7 py-20 flex flex-col items-center justify-center text-slate-400">
                <div class="w-8 h-8 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                <span class="text-xs">Carregando horários...</span>
            </div>
        `;

        try {
            const response = await fetch(`/api/disponibilidade?mes=${mesParam}`);
            const data = await response.json();
            monthDataCache = data.dias || {};
            renderCalendarGrid(dateObj, monthDataCache);
        } catch (error) {
            console.error("Erro ao carregar disponibilidade:", error);
            grid.innerHTML = `<div class="col-span-7 py-10 text-center text-rose-400 text-sm font-semibold">Falha ao carregar dados do calendário. Tente recarregar a página.</div>`;
        }
    }

    function renderCalendarGrid(dateObj, daysData) {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';

        const year = dateObj.getFullYear();
        const month = dateObj.getMonth();

        const firstDayIndex = new Date(year, month, 1).getDay();
        const totalDaysInMonth = new Date(year, month + 1, 0).getDate();

        // Empty cells before start of month
        for (let i = 0; i < firstDayIndex; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.className = "h-24 rounded-2xl bg-slate-900/30 border border-slate-800/40 opacity-40";
            grid.appendChild(emptyCell);
        }

        const todayStr = new Date().toISOString().split('T')[0];

        // Days of month
        for (let day = 1; day <= totalDaysInMonth; day++) {
            const dayFormatted = String(day).padStart(2, '0');
            const monthFormatted = String(month + 1).padStart(2, '0');
            const dateStr = `${year}-${monthFormatted}-${dayFormatted}`;

            const info = daysData[dateStr] || {
                status_dia: 'vermelho',
                is_weekend: false,
                turnos: { manha: { status: 'livre' }, tarde: { status: 'livre' } }
            };

            const dayCard = document.createElement('div');

            let colorClasses = "";
            let badgeText = "";
            let badgeBg = "";

            if (info.status_dia === 'verde') {
                colorClasses = "bg-emerald-950/20 border-emerald-800/40 hover:border-emerald-500 hover:shadow-lg hover:shadow-emerald-950/40 cursor-pointer";
                badgeText = "Disponível";
                badgeBg = "bg-emerald-500/20 text-emerald-300 border-emerald-500/30";
            } else if (info.status_dia === 'amarelo') {
                colorClasses = "bg-amber-950/20 border-amber-800/40 hover:border-amber-500 cursor-pointer";
                badgeText = "Agendado";
                badgeBg = "bg-amber-500/20 text-amber-300 border-amber-500/30";
            } else {
                colorClasses = "bg-rose-950/20 border-rose-900/30 opacity-75 cursor-not-allowed";
                badgeText = info.is_weekend ? "Fim de Semana" : "Indisponível";
                badgeBg = "bg-rose-500/20 text-rose-300 border-rose-500/30";
            }

            const isPastDate = new Date(dateStr) < new Date(todayStr);
            if (isPastDate) {
                colorClasses = "bg-slate-900/40 border-slate-800 opacity-50 cursor-not-allowed";
                badgeText = "Passado";
                badgeBg = "bg-slate-800 text-slate-400 border-slate-700";
            }

            const isToday = dateStr === todayStr;

            dayCard.className = `h-24 sm:h-28 rounded-2xl p-2.5 border flex flex-col justify-between transition-all relative group ${colorClasses}`;

            dayCard.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-sm font-extrabold ${isToday ? 'w-6 h-6 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center shadow-md' : 'text-white'}">
                        ${day}
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded border ${badgeBg}">
                        ${badgeText}
                    </span>
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between text-[10px] text-slate-300">
                        <span>Manhã:</span>
                        <span class="font-semibold ${getTurnoBadgeColor(info.turnos?.manha?.status)}">${formatTurnoText(info.turnos?.manha?.status)}</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-300">
                        <span>Tarde:</span>
                        <span class="font-semibold ${getTurnoBadgeColor(info.turnos?.tarde?.status)}">${formatTurnoText(info.turnos?.tarde?.status)}</span>
                    </div>
                </div>
            `;

            if (!isPastDate && info.status_dia !== 'vermelho') {
                dayCard.addEventListener('click', () => openBookingModal(dateStr, info));
            }

            grid.appendChild(dayCard);
        }
    }

    function getTurnoBadgeColor(status) {
        if (status === 'livre') return 'text-emerald-400';
        if (status === 'agendado') return 'text-amber-400';
        return 'text-rose-400';
    }

    function formatTurnoText(status) {
        if (status === 'livre') return 'Livre';
        if (status === 'agendado') return 'Agendado';
        return 'Bloqueado';
    }

    function openBookingModal(dateStr, dayInfo) {
        document.getElementById('input-data').value = dateStr;

        const dateParts = dateStr.split('-');
        const dateFormatted = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
        document.getElementById('modal-date-display').innerText = `Visita para o dia ${dateFormatted}`;

        // Reset inputs
        document.getElementById('modal-alert').classList.add('hidden');
        document.getElementById('booking-form').reset();
        document.getElementById('input-data').value = dateStr;

        // Turno Manhã setup
        const radioManha = document.getElementById('radio-manha');
        const badgeManha = document.getElementById('badge-manha');
        const optionManha = document.getElementById('option-manha');

        if (dayInfo.turnos?.manha?.status === 'livre') {
            radioManha.disabled = false;
            radioManha.checked = true;
            badgeManha.className = "text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
            badgeManha.innerText = "Livre";
            optionManha.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            radioManha.disabled = true;
            radioManha.checked = false;
            badgeManha.className = "text-[10px] font-bold px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30";
            badgeManha.innerText = dayInfo.turnos?.manha?.status === 'agendado' ? 'Agendado' : 'Bloqueado';
            optionManha.classList.add('opacity-50', 'cursor-not-allowed');
        }

        // Turno Tarde setup
        const radioTarde = document.getElementById('radio-tarde');
        const badgeTarde = document.getElementById('badge-tarde');
        const optionTarde = document.getElementById('option-tarde');

        if (dayInfo.turnos?.tarde?.status === 'livre') {
            radioTarde.disabled = false;
            if (!radioManha.checked) radioTarde.checked = true;
            badgeTarde.className = "text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30";
            badgeTarde.innerText = "Livre";
            optionTarde.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            radioTarde.disabled = true;
            radioTarde.checked = false;
            badgeTarde.className = "text-[10px] font-bold px-2 py-0.5 rounded bg-rose-500/20 text-rose-400 border border-rose-500/30";
            badgeTarde.innerText = dayInfo.turnos?.tarde?.status === 'agendado' ? 'Agendado' : 'Bloqueado';
            optionTarde.classList.add('opacity-50', 'cursor-not-allowed');
        }

        document.getElementById('booking-modal').classList.remove('hidden');
    }

    function closeBookingModal() {
        document.getElementById('booking-modal').classList.add('hidden');
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const alertEl = document.getElementById('modal-alert');
        const btnText = document.getElementById('btn-submit-text');
        const btnSpinner = document.getElementById('btn-submit-spinner');
        const btnSubmit = document.getElementById('btn-submit-booking');

        alertEl.classList.add('hidden');
        btnSubmit.disabled = true;
        btnText.innerText = "Processando...";
        btnSpinner.classList.remove('hidden');

        const formData = new FormData(e.target);
        const payload = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/api/agendar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                alertEl.className = "my-4 p-4 rounded-xl text-sm font-semibold bg-emerald-950/80 border border-emerald-600 text-emerald-300 flex items-center gap-2";
                alertEl.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5"></i><span>${result.message}</span>`;
                alertEl.classList.remove('hidden');
                lucide.createIcons();

                setTimeout(() => {
                    closeBookingModal();
                    loadMonthAvailability(currentMonthDate);
                }, 2000);
            } else {
                let errorMsg = result.message || 'Erro ao processar o agendamento.';
                if (result.errors) {
                    errorMsg = Object.values(result.errors).flat().join('<br>');
                }
                alertEl.className = "my-4 p-4 rounded-xl text-sm font-semibold bg-rose-950/80 border border-rose-600 text-rose-300 flex items-start gap-2";
                alertEl.innerHTML = `<i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 mt-0.5"></i><div>${errorMsg}</div>`;
                alertEl.classList.remove('hidden');
                lucide.createIcons();
            }
        } catch (error) {
            console.error("Erro na requisição:", error);
            alertEl.className = "my-4 p-4 rounded-xl text-sm font-semibold bg-rose-950/80 border border-rose-600 text-rose-300 flex items-center gap-2";
            alertEl.innerHTML = `<i data-lucide="alert-triangle" class="w-5 h-5"></i><span>Erro de conexão. Verifique sua rede e tente novamente.</span>`;
            alertEl.classList.remove('hidden');
            lucide.createIcons();
        } finally {
            btnSubmit.disabled = false;
            btnText.innerText = "Confirmar Agendamento";
            btnSpinner.classList.add('hidden');
        }
    }
</script>
@endpush
