<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Acesso') — Sabenta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
        }
        .auth-panel {
            width: 42%;
            background: linear-gradient(155deg, #1a3578 0%, #152D6E 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }
        .auth-panel::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: rgba(99, 179, 245, 0.08);
        }
        .auth-panel::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 250px;
            height: 250px;
            border-radius: 50%;
            background: rgba(74, 142, 212, 0.07);
        }
        .auth-form-area {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--sabenta-bg);
            padding: 2rem;
        }
        .auth-form-card {
            width: 100%;
            max-width: 430px;
        }
        .auth-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .auth-feature-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
            color: var(--sabenta-accent);
            flex-shrink: 0;
        }
        .auth-feature-title {
            font-family: var(--font-heading);
            font-size: 0.9375rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.2rem;
        }
        .auth-feature-desc {
            font-size: 0.8125rem;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.45;
        }
        @media (max-width: 767.98px) {
            .auth-panel { display: none; }
            .auth-form-area { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body style="background-color: var(--sabenta-bg);">

<div class="auth-wrapper">

    {{-- Painel esquerdo --}}
    <div class="auth-panel d-none d-md-flex">
        <div>
            <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; margin-bottom: 3.5rem;">
                <img src="{{ asset('images/logo-sabenta.png') }}" alt="Sabenta" height="32"
                     style="filter: brightness(0) invert(1);">
            </a>

            <h2 style="font-family: var(--font-heading); font-size: 1.625rem; font-weight: 800; color: #fff; line-height: 1.3; margin-bottom: 0.75rem;">
                Organize sua clínica.<br>Foque nos seus pacientes.
            </h2>
            <p style="font-size: 0.9rem; color: rgba(255,255,255,0.55); margin-bottom: 2.5rem; line-height: 1.65;">
                Agenda inteligente, lembretes automáticos e gestão financeira em um só lugar.
            </p>

            <div class="auth-feature-item">
                <div class="auth-feature-icon"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div class="auth-feature-title">Agenda online 24h</div>
                    <div class="auth-feature-desc">Seus pacientes agendam sozinhos, você só confirma.</div>
                </div>
            </div>

            <div class="auth-feature-item">
                <div class="auth-feature-icon"><i class="bi bi-whatsapp"></i></div>
                <div>
                    <div class="auth-feature-title">Lembretes automáticos</div>
                    <div class="auth-feature-desc">Reduza faltas com confirmações via WhatsApp.</div>
                </div>
            </div>

            <div class="auth-feature-item">
                <div class="auth-feature-icon"><i class="bi bi-shield-lock"></i></div>
                <div>
                    <div class="auth-feature-title">Privacidade total</div>
                    <div class="auth-feature-desc">Dados criptografados, em conformidade com o CFP e LGPD.</div>
                </div>
            </div>
        </div>

        <div>
            <p style="font-size: 0.775rem; color: rgba(255,255,255,0.3);">
                &copy; {{ date('Y') }} Sabenta. Todos os direitos reservados.
            </p>
        </div>
    </div>

    {{-- Área do formulário --}}
    <div class="auth-form-area">
        <div class="auth-form-card">
            <div class="text-center mb-4 d-md-none">
                <img src="{{ asset('images/logo-sabenta.png') }}" alt="Sabenta" height="36">
            </div>
            @yield('content')
        </div>
    </div>

</div>

@stack('scripts')
</body>
</html>
