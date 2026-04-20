<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sabenta') — Agendamento Online</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #fff; }

        .public-navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--sabenta-border);
            padding: 0.875rem 0;
        }

        .public-footer {
            background-color: var(--sabenta-primary-dark);
            color: rgba(255, 255, 255, 0.65);
            padding: 2.5rem 0 1.5rem;
            font-size: 0.8375rem;
        }

        .public-footer a {
            color: rgba(255, 255, 255, 0.55);
            text-decoration: none;
        }

        .public-footer a:hover {
            color: #fff;
        }

        .public-footer__brand {
            font-family: var(--font-heading);
            font-size: 1.125rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.5rem;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="public-navbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-sabenta.png') }}" alt="Sabenta" height="32">
            </a>
            <div class="d-flex align-items-center gap-3">
                @yield('nav-extra')
                <a href="{{ route('home') }}" class="d-none d-md-block"
                   style="font-size: 0.875rem; color: var(--sabenta-text-muted); font-family: var(--font-heading); font-weight: 500;">
                    Para profissionais
                </a>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex">
                    Entrar
                </a>
                <a href="{{ route('register') }}" class="btn btn-sm btn-primary">
                    Criar conta grátis
                </a>
            </div>
        </div>
    </div>
</nav>

{{-- Conteúdo --}}
@yield('content')

{{-- Footer --}}
<footer class="public-footer">
    <div class="container">
        <div class="row g-4 mb-3">
            <div class="col-md-4">
                <div class="public-footer__brand">Sabenta</div>
                <p style="color: rgba(255,255,255,0.45); font-size: 0.8rem; line-height: 1.6;">
                    Plataforma de agendamento para psicólogos e terapeutas.<br>
                    Simples, segura e profissional.
                </p>
            </div>
            <div class="col-6 col-md-2 offset-md-2">
                <div style="font-family: var(--font-heading); font-weight: 600; color: rgba(255,255,255,0.7); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem;">Produto</div>
                <ul class="list-unstyled" style="font-size: 0.8375rem; line-height: 2;">
                    <li><a href="{{ route('planos') }}">Planos e preços</a></li>
                    <li><a href="#">Funcionalidades</a></li>
                    <li><a href="#">Para psicólogos</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <div style="font-family: var(--font-heading); font-weight: 600; color: rgba(255,255,255,0.7); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem;">Legal</div>
                <ul class="list-unstyled" style="font-size: 0.8375rem; line-height: 2;">
                    <li><a href="{{ route('privacidade') }}">Privacidade</a></li>
                    <li><a href="#">Termos de uso</a></li>
                    <li><a href="#">LGPD</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <div style="font-family: var(--font-heading); font-weight: 600; color: rgba(255,255,255,0.7); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.75rem;">Suporte</div>
                <ul class="list-unstyled" style="font-size: 0.8375rem; line-height: 2;">
                    <li><a href="#">Central de ajuda</a></li>
                    <li><a href="#">Contato</a></li>
                </ul>
            </div>
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.25rem; font-size: 0.775rem; color: rgba(255,255,255,0.3);">
            &copy; {{ date('Y') }} Sabenta. Todos os direitos reservados. — CNPJ 00.000.000/0001-00
        </div>
    </div>
</footer>

@stack('modals')
@stack('scripts')
</body>
</html>
