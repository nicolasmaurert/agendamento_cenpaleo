@extends('layouts.app')

@section('title', 'Login Administrativo - Cenpaleo')

@section('content')
<div class="max-w-md mx-auto py-12">
    <div class="glass-panel rounded-3xl p-8 border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <div class="text-center space-y-2 mb-8">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-900/40 mb-3">
                <i data-lucide="lock" class="w-7 h-7 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Acesso Administrativo</h2>
            <p class="text-slate-400 text-xs">Informe seu usuário e senha definidos no ambiente para gerenciar os agendamentos da exposição.</p>
        </div>

        @if($errors->has('login') || $errors->has('auth'))
            <div class="mb-6 p-4 rounded-xl bg-rose-950/80 border border-rose-600/60 text-rose-300 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ $errors->first('login') ?: $errors->first('auth') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-950/80 border border-emerald-600/60 text-emerald-300 text-xs font-semibold flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label for="username" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Usuário</label>
                <div class="relative">
                    <input type="text" id="username" name="username" required value="{{ old('username', 'admin') }}"
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <i data-lucide="user" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5"></i>
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Senha</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required value="cenpaleo2026"
                           class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <i data-lucide="key-round" class="w-4 h-4 text-slate-500 absolute left-3.5 top-3.5"></i>
                </div>
            </div>

            <div class="glass-card p-3.5 rounded-xl border border-slate-800 text-[11px] text-slate-400 space-y-1">
                <div class="font-semibold text-slate-300 flex items-center gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span>Credenciais Padrão (.env):</span>
                </div>
                <div>Usuário: <code class="text-emerald-300 font-mono">admin</code> | Senha: <code class="text-emerald-300 font-mono">cenpaleo2026</code></div>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-900/40 transition-all flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                <span>Entrar no Painel</span>
            </button>
        </form>
    </div>
</div>
@endsection
