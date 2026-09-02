<!DOCTYPE html>
<html lang="pt-BR" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Exposição Cenpaleo - Agendamento de Visitas')</title>

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b1120;
            background-image: radial-gradient(at 0% 0%, rgba(30, 41, 59, 0.5) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.8) 0px, transparent 50%);
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col antialiased text-slate-200">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 glass-panel border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('public.calendar') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-900/30 group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="bone" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-white group-hover:text-emerald-400 transition-colors">Exposição Cenpaleo</h1>
                    <p class="text-xs text-slate-400 font-medium">Sistema de Agendamento de Visitas Guiadas</p>
                </div>
            </a>

            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/80 text-xs font-semibold text-emerald-400 border border-emerald-500/20">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>Em Conformidade com a LGPD</span>
                </div>

                @if(session('admin_logged_in'))
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-900/40 transition-all flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Painel Admin</span>
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-all flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                        <span>Área Restrita</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-800/80 py-8 bg-slate-950/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <div class="flex items-center gap-2">
                <i data-lucide="landmark" class="w-4 h-4 text-emerald-500"></i>
                <span>Centro de Pesquisas Paleontológicas – Cenpaleo</span>
            </div>
            <div>
                <span>© {{ date('Y') }} Todos os direitos reservados. Projeto educativo e científico.</span>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="openLgpdModal()" class="hover:text-emerald-400 underline transition-colors">Termos de Privacidade (LGPD)</button>
            </div>
        </div>
    </footer>

    <!-- LGPD Info Modal -->
    <div id="lgpd-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="glass-panel max-w-lg w-full rounded-2xl p-6 shadow-2xl border border-slate-700">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2 text-emerald-400">
                    <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    <h3 class="font-bold text-lg text-white">Política de Privacidade e LGPD</h3>
                </div>
                <button onclick="closeLgpdModal()" class="text-slate-400 hover:text-white transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="py-4 text-sm text-slate-300 space-y-3 max-h-96 overflow-y-auto">
                <p>O <strong>Centro de Pesquisas Paleontológicas (Cenpaleo)</strong> respeita a sua privacidade. Os dados fornecidos no formulário de agendamento (Nome, Instituição, Telefone e E-mail) são coletados exclusivamente para fins de organização, validação e comunicação de visitas guiadas.</p>
                <ul class="list-disc list-inside space-y-1 text-slate-400">
                    <li>Seus dados <strong>nunca</strong> serão compartilhados com terceiros ou usados para fins comerciais.</li>
                    <li>O cadastro é mantido de forma segura durante o período letivo/operacional do agendamento.</li>
                    <li>Você pode solicitar a alteração ou remoção de seus dados enviando uma mensagem aos administradores.</li>
                </ul>
            </div>
            <div class="pt-4 border-t border-slate-800 text-right">
                <button onclick="closeLgpdModal()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg text-sm transition-all">Entendi e Aceito</button>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        function openLgpdModal() {
            document.getElementById('lgpd-modal').classList.remove('hidden');
        }
        function closeLgpdModal() {
            document.getElementById('lgpd-modal').classList.add('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
