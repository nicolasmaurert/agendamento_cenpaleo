@extends('layouts.app')

@section('title', 'Acesso Administrativo - CENPALEO')

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <div class="grid lg:grid-cols-2 museum-card rounded-[2rem] overflow-hidden max-w-5xl mx-auto">
        <div class="bg-cenpaleo-navy text-white p-8 sm:p-12 relative overflow-hidden flex flex-col justify-between min-h-[360px]">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full border-[52px] border-cenpaleo-cyan/15"></div>
            <div class="absolute top-12 right-12 w-16 h-16 bg-cenpaleo-orange/30 rotate-45 rounded-xl"></div>
            <div class="relative">
                <p class="text-cenpaleo-cyan text-[11px] uppercase tracking-[.18em] font-bold">Equipe CENPALEO</p>
                <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold leading-tight">Organize as visitas do museu em um só lugar.</h1>
                <p class="mt-5 text-sm leading-7 text-white/65">Consulte a agenda mensal, veja quem visitará o museu em cada turno e controle bloqueios institucionais.</p>
            </div>
            <div class="relative flex items-center gap-3 text-xs text-white/70"><span class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center"><i data-lucide="shield-check" class="w-5 h-5 text-cenpaleo-cyan"></i></span>Acesso exclusivo para a equipe autorizada.</div>
        </div>

        <div class="bg-white p-8 sm:p-12 flex flex-col justify-center">
            <div class="mb-8">
                <span class="w-12 h-12 rounded-full bg-violet-50 text-cenpaleo-purple flex items-center justify-center mb-4"><i data-lucide="lock-keyhole" class="w-5 h-5"></i></span>
                <h2 class="text-2xl font-extrabold text-cenpaleo-navy">Acesso administrativo</h2>
                <p class="mt-2 text-xs leading-5 text-slate-500">Informe as credenciais configuradas para o sistema de agendamentos.</p>
            </div>

            @if($errors->has('login') || $errors->has('auth'))
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-start gap-2"><i data-lucide="triangle-alert" class="w-4 h-4 shrink-0"></i><span>{{ $errors->first('login') ?: $errors->first('auth') }}</span></div>
            @endif
            @if(session('success'))
                <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-start gap-2"><i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i><span>{{ session('success') }}</span></div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf
                <div><label for="username" class="block text-[10px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Usuário</label><div class="relative"><i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-4 top-3.5"></i><input type="text" id="username" name="username" required value="{{ old('username') }}" autocomplete="username" class="museum-field pl-11 pr-4 py-3 text-sm"></div></div>
                <div><label for="password" class="block text-[10px] font-bold text-cenpaleo-navy uppercase tracking-wider mb-1.5">Senha</label><div class="relative"><i data-lucide="key-round" class="w-4 h-4 text-slate-400 absolute left-4 top-3.5"></i><input type="password" id="password" name="password" required autocomplete="current-password" class="museum-field pl-11 pr-4 py-3 text-sm"></div></div>
                <button type="submit" class="w-full py-3.5 px-5 rounded-full bg-cenpaleo-purple hover:bg-violet-700 text-white font-bold text-sm shadow-lg shadow-violet-200 transition-colors flex items-center justify-center gap-2"><i data-lucide="log-in" class="w-4 h-4"></i>Entrar no painel</button>
            </form>
        </div>
    </div>
</section>
@endsection
