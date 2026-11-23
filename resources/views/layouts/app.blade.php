<!DOCTYPE html>
<html lang="pt-BR" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#43bdd3">
    <title>@yield('title', 'CENPALEO - Agendamento de Visitas')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root { color-scheme: light; }
        body {
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 8% 20%, rgba(67, 189, 211, .08), transparent 24rem), radial-gradient(circle at 92% 45%, rgba(107, 79, 217, .06), transparent 28rem), #f7f8f8;
            color: #334155;
        }
        .museum-card { background: rgba(255, 255, 255, .96); border: 1px solid rgba(23, 50, 77, .08); box-shadow: 0 18px 50px rgba(23, 50, 77, .10); }
        .museum-field { width: 100%; border: 1px solid #dce3e8; border-radius: .85rem; background: #fff; color: #17324d; transition: border-color .2s, box-shadow .2s; }
        .museum-field:focus { border-color: #43bdd3; box-shadow: 0 0 0 3px rgba(67, 189, 211, .16); outline: none; }
        .section-kicker { color: #f7941d; font-size: .72rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .brand-rule { width: 4.5rem; height: .3rem; border-radius: 999px; background: linear-gradient(90deg, #f7941d 0 33%, #43bdd3 33% 66%, #6b4fd9 66%); }
        .modal-backdrop { background: rgba(23, 50, 77, .66); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        [data-lucide] { stroke-width: 1.8; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col antialiased">
    <div class="bg-cenpaleo-cyan text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-10 py-2 flex flex-wrap items-center justify-between gap-x-6 gap-y-2 text-[11px] sm:text-xs font-medium">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <a href="tel:+554736415514" class="flex items-center gap-1.5 hover:text-cenpaleo-navy transition-colors"><i data-lucide="phone" class="w-3.5 h-3.5"></i><span>(47) 3641-5514</span></a>
                <a href="mailto:cenpaleo@unc.br" class="flex items-center gap-1.5 hover:text-cenpaleo-navy transition-colors"><i data-lucide="mail" class="w-3.5 h-3.5"></i><span>cenpaleo@unc.br</span></a>
            </div>
            <div class="hidden md:flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i><span>Av. Presidente Nereu Ramos, 1071 — Mafra/SC</span></div>
        </div>
    </div>

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-24 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('public.calendar') }}" class="shrink-0" aria-label="Página inicial do agendamento CENPALEO">
                <img src="{{ asset('images/cenpaleo/logo.png') }}" alt="CENPALEO - Centro de Pesquisa Paleontológica" class="h-16 sm:h-[72px] w-auto">
            </a>
            <nav class="flex items-center gap-2 sm:gap-5 text-xs font-bold uppercase tracking-wide text-cenpaleo-navy">
                <a href="{{ route('public.calendar') }}" class="hidden sm:inline-flex py-2 border-b-2 border-cenpaleo-orange text-cenpaleo-orange">Agendamento</a>
                <a href="https://cenpaleo.unc.br/" target="_blank" rel="noopener noreferrer" class="hidden lg:inline-flex py-2 hover:text-cenpaleo-purple transition-colors">Conheça o CENPALEO</a>
                @if(session('admin_logged_in'))
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2.5 rounded-full bg-cenpaleo-purple text-white hover:bg-violet-700 transition-colors shadow-md shadow-violet-200"><i data-lucide="layout-dashboard" class="w-4 h-4"></i><span class="hidden sm:inline">Painel Admin</span><span class="sm:hidden">Admin</span></a>
                @else
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2.5 rounded-full border border-slate-200 hover:border-cenpaleo-cyan hover:text-cenpaleo-cyan-dark transition-colors"><i data-lucide="lock-keyhole" class="w-4 h-4"></i><span class="hidden sm:inline">Área Restrita</span><span class="sm:hidden">Admin</span></a>
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-grow w-full">@yield('content')</main>

    <footer class="mt-auto bg-cenpaleo-charcoal text-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
            <div>
                <div class="inline-flex bg-white rounded-xl px-4 py-3 mb-4"><img src="{{ asset('images/cenpaleo/logo.png') }}" alt="CENPALEO" class="h-12 w-auto"></div>
                <p class="text-xs leading-6 text-slate-400">Centro de Pesquisa Paleontológica e Museu da Terra e da Vida da Universidade do Contestado.</p>
            </div>
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Visite-nos</h2>
                <div class="space-y-3 text-xs leading-5 text-slate-400">
                    <p class="flex gap-2"><i data-lucide="map-pin" class="w-4 h-4 text-cenpaleo-cyan shrink-0"></i><span>Bloco E da UnC — Av. Pres. Nereu Ramos, 1071, Jardim Moinho, Mafra/SC</span></p>
                    <p class="flex gap-2"><i data-lucide="clock-3" class="w-4 h-4 text-cenpaleo-orange shrink-0"></i><span>Visitas mediante agendamento prévio.</span></p>
                </div>
            </div>
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Contato</h2>
                <div class="space-y-3 text-xs text-slate-400">
                    <a href="mailto:cenpaleo@unc.br" class="flex items-center gap-2 hover:text-cenpaleo-cyan transition-colors"><i data-lucide="mail" class="w-4 h-4"></i>cenpaleo@unc.br</a>
                    <a href="tel:+554736415514" class="flex items-center gap-2 hover:text-cenpaleo-cyan transition-colors"><i data-lucide="phone" class="w-4 h-4"></i>(47) 3641-5514</a>
                    <button type="button" onclick="openLgpdModal()" class="flex items-center gap-2 hover:text-cenpaleo-cyan transition-colors"><i data-lucide="shield-check" class="w-4 h-4"></i>Política de Privacidade</button>
                </div>
            </div>
        </div>
        <div class="border-t border-white/10 bg-[#242424]"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 text-center text-[11px] text-slate-500">© {{ date('Y') }} CENPALEO — Universidade do Contestado — FUnC</div></div>
    </footer>

    <div id="lgpd-modal" class="fixed inset-0 z-50 hidden modal-backdrop items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="lgpd-title">
        <div class="museum-card max-w-lg w-full rounded-3xl p-6 sm:p-8">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3"><span class="w-10 h-10 rounded-full bg-cyan-50 text-cenpaleo-cyan-dark flex items-center justify-center"><i data-lucide="shield-check" class="w-5 h-5"></i></span><h2 id="lgpd-title" class="font-bold text-lg text-cenpaleo-navy">Privacidade e LGPD</h2></div>
                <button type="button" onclick="closeLgpdModal()" class="p-2 rounded-full text-slate-400 hover:bg-slate-100 hover:text-cenpaleo-navy transition-colors" aria-label="Fechar"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div class="py-5 text-sm text-slate-600 space-y-3 leading-6 max-h-96 overflow-y-auto">
                <p>O <strong class="text-cenpaleo-navy">CENPALEO</strong> coleta os dados do formulário exclusivamente para organizar, validar e comunicar as visitas agendadas.</p>
                <ul class="list-disc pl-5 space-y-2"><li>Os dados não são usados para fins comerciais.</li><li>O acesso é restrito à equipe responsável pelos agendamentos.</li><li>Você pode solicitar correção ou remoção pelos canais oficiais de contato.</li></ul>
            </div>
            <div class="pt-4 border-t border-slate-100 text-right"><button type="button" onclick="closeLgpdModal()" class="px-5 py-2.5 bg-cenpaleo-purple hover:bg-violet-700 text-white font-bold rounded-full text-xs transition-colors">Entendi</button></div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        function openLgpdModal() {
            const modal = document.getElementById('lgpd-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeLgpdModal() {
            const modal = document.getElementById('lgpd-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
    @stack('scripts')
</body>
</html>
