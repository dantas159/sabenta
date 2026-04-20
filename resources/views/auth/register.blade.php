@extends('layouts.auth')

@section('title', 'Criar conta')

@section('content')

<div x-data="{
    showPass: false,
    showPassConf: false,
    step: 1,
    nome: '',
    email: '',
    whatsapp: '',
    senha: '',
    senhaConf: '',
    termos: false,
    loading: false,
    senhaForca: 0,
    calcForca(v) {
        let s = 0;
        if (v.length >= 8) s++;
        if (/[A-Z]/.test(v)) s++;
        if (/[0-9]/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        this.senhaForca = s;
    },
    get forcaLabel() {
        return ['', 'Fraca', 'Razoável', 'Boa', 'Forte'][this.senhaForca];
    },
    get forcaColor() {
        return ['', '#dc2626', '#d97706', '#1d9e75', '#1d9e75'][this.senhaForca];
    },
    get step1Valido() {
        return this.nome.trim().length >= 3 && this.email.includes('@') && this.whatsapp.length >= 14;
    },
    get step2Valido() {
        return this.senhaForca >= 2 && this.senha === this.senhaConf && this.termos;
    },
}">

    {{-- Cabeçalho --}}
    <div class="mb-4">
        <h2 style="font-family:var(--font-heading);font-size:1.5rem;font-weight:800;color:var(--sabenta-text);margin-bottom:0.25rem;">
            Criar conta grátis
        </h2>
        <p style="font-size:0.875rem;color:var(--sabenta-text-muted);margin:0;">
            14 dias grátis · Sem cartão de crédito
        </p>
    </div>

    {{-- Indicador de etapas --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <div style="height:4px;flex:1;border-radius:99px;background:var(--sabenta-primary);transition:all .3s;"></div>
        <div style="height:4px;flex:1;border-radius:99px;transition:all .3s;"
             :style="step >= 2 ? 'background:var(--sabenta-primary)' : 'background:var(--sabenta-border)'"></div>
        <span style="font-size:0.75rem;color:var(--sabenta-text-muted);white-space:nowrap;font-family:var(--font-heading);font-weight:600;"
              x-text="'Etapa ' + step + ' de 2'"></span>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3" style="font-size:0.875rem;border-radius:10px;">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success py-2 mb-3" style="font-size:0.875rem;border-radius:10px;">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- ── Etapa 1: Dados pessoais ─────────────────────────── --}}
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-2"
             x-transition:enter-end="opacity-100 translate-x-0">

            <div class="mb-3">
                <label class="form-label">Nome completo *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       x-model="nome"
                       value="{{ old('name') }}"
                       placeholder="Dra. Maria Silva"
                       autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">E-mail *</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       x-model="email"
                       value="{{ old('email') }}"
                       placeholder="você@email.com"
                       autocomplete="email">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">WhatsApp *</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:var(--sabenta-bg);border-color:var(--sabenta-border);">
                        <i class="bi bi-whatsapp" style="color:#25d366;"></i>
                    </span>
                    <input type="tel" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror"
                           x-model="whatsapp"
                           value="{{ old('whatsapp') }}"
                           placeholder="(11) 99999-9999"
                           data-mask="phone"
                           autocomplete="tel">
                    @error('whatsapp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-text">Usado para lembretes e notificações da plataforma.</div>
            </div>

            <button type="button" class="btn btn-primary w-100"
                    :disabled="!step1Valido"
                    @click="step = 2">
                Continuar <i class="bi bi-arrow-right ms-1"></i>
            </button>

        </div>

        {{-- ── Etapa 2: Senha e termos ──────────────────────────── --}}
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-2"
             x-transition:enter-end="opacity-100 translate-x-0"
             style="display:none;">

            <div class="mb-3">
                <label class="form-label">Senha *</label>
                <div class="input-group">
                    <input :type="showPass ? 'text' : 'password'"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           x-model="senha"
                           @input="calcForca($event.target.value)"
                           placeholder="Mín. 8 caracteres"
                           autocomplete="new-password">
                    <button type="button" class="input-group-text"
                            style="cursor:pointer;background:var(--sabenta-bg);border-color:var(--sabenta-border);"
                            @click="showPass = !showPass">
                        <i class="bi" :class="showPass ? 'bi-eye-slash' : 'bi-eye'"
                           style="color:var(--sabenta-text-muted);"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Barra de força --}}
                <template x-if="senha.length > 0">
                    <div class="mt-2" x-transition>
                        <div style="display:flex;gap:4px;margin-bottom:4px;">
                            <template x-for="i in 4" :key="i">
                                <div style="height:3px;flex:1;border-radius:99px;transition:background .2s;"
                                     :style="i <= senhaForca
                                        ? 'background:' + forcaColor
                                        : 'background:var(--sabenta-border)'">
                                </div>
                            </template>
                        </div>
                        <span style="font-size:0.75rem;font-weight:600;"
                              :style="'color:' + forcaColor" x-text="forcaLabel"></span>
                    </div>
                </template>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmar senha *</label>
                <div class="input-group">
                    <input :type="showPassConf ? 'text' : 'password'"
                           name="password_confirmation"
                           class="form-control"
                           x-model="senhaConf"
                           placeholder="Repita a senha"
                           autocomplete="new-password">
                    <button type="button" class="input-group-text"
                            style="cursor:pointer;background:var(--sabenta-bg);border-color:var(--sabenta-border);"
                            @click="showPassConf = !showPassConf">
                        <i class="bi" :class="showPassConf ? 'bi-eye-slash' : 'bi-eye'"
                           style="color:var(--sabenta-text-muted);"></i>
                    </button>
                </div>
                <template x-if="senhaConf.length > 0 && senha !== senhaConf">
                    <div style="font-size:0.78rem;color:#dc2626;margin-top:4px;" x-transition>
                        <i class="bi bi-x-circle me-1"></i>As senhas não coincidem.
                    </div>
                </template>
                <template x-if="senhaConf.length > 0 && senha === senhaConf">
                    <div style="font-size:0.78rem;color:var(--sabenta-success, #1d9e75);margin-top:4px;" x-transition>
                        <i class="bi bi-check-circle me-1"></i>Senhas iguais.
                    </div>
                </template>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="termos" x-model="termos">
                    <label class="form-check-label" for="termos" style="font-size:0.8375rem;line-height:1.5;">
                        Li e aceito os
                        <a href="{{ route('privacidade') }}" target="_blank">Termos de Uso</a>
                        e a
                        <a href="{{ route('privacidade') }}" target="_blank">Política de Privacidade</a>.
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-outline-secondary"
                        style="flex:0 0 auto;"
                        @click="step = 1">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button type="submit" class="btn btn-primary flex-fill"
                        :disabled="!step2Valido || loading"
                        @click="loading = true">
                    <span x-show="!loading">
                        <i class="bi bi-check-circle me-1"></i>Criar minha conta
                    </span>
                    <span x-show="loading">
                        <i class="bi bi-arrow-repeat spin me-1"></i>Criando conta…
                    </span>
                </button>
            </div>

        </div>

    </form>

    {{-- Rodapé --}}
    <div class="text-center mt-3" style="font-size:0.8375rem;color:var(--sabenta-text-muted);">
        Já tem uma conta?
        <a href="{{ route('login') }}" style="font-weight:600;">Entrar</a>
    </div>

    <div class="mt-4 p-3 rounded-3 text-center"
         style="background:var(--sabenta-bg);font-size:0.75rem;color:var(--sabenta-text-muted);line-height:1.6;">
        <i class="bi bi-shield-lock me-1"></i>
        Seus dados são protegidos com criptografia e em conformidade com a LGPD.
    </div>

</div>

@endsection
