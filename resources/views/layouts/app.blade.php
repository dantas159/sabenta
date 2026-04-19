<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel') — Sabenta</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<div class="sabenta-wrapper" x-data="{ sidebarOpen: false }">

    {{-- =====================================================
         SIDEBAR
         ===================================================== --}}
    <aside class="sabenta-sidebar" :class="{ 'show': sidebarOpen }">

        {{-- Logo --}}
        <div class="sabenta-sidebar__logo">
            <a href="{{ route('painel.dashboard') }}">
                <img src="{{ asset('images/logo-sabenta.png') }}" alt="Sabenta" height="34"
                     style="filter: brightness(0) invert(1);">
            </a>
        </div>

        {{-- Navegação --}}
        <nav class="sabenta-sidebar__nav" aria-label="Menu principal">

            {{-- Painel --}}
            <a href="{{ route('painel.dashboard') }}"
               class="sidebar-nav-item {{ request()->routeIs('painel.dashboard') ? 'active' : '' }}"
               @click="sidebarOpen = false">
                <i class="bi bi-speedometer2"></i>
                <span>Painel</span>
            </a>

            {{-- Agenda (com sub-menu) --}}
            <div x-data="{ open: {{ request()->routeIs('painel.agenda.*') ? 'true' : 'false' }} }">
                <button type="button"
                        class="sidebar-nav-item {{ request()->routeIs('painel.agenda.*') ? 'active' : '' }}"
                        @click="open = !open"
                        style="text-align: left;">
                    <i class="bi bi-calendar3"></i>
                    <span style="flex: 1; text-align: left;">Agenda</span>
                    <i class="bi bi-chevron-down nav-chevron"
                       :style="open ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'"></i>
                </button>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 transform -translate-y-1"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="display: none;">
                    <div class="sidebar-subnav">
                        <a href="{{ route('painel.agenda.dia') }}"
                           class="sidebar-nav-item {{ request()->routeIs('painel.agenda.dia') ? 'active' : '' }}"
                           @click="sidebarOpen = false">
                            Dia
                        </a>
                        <a href="{{ route('painel.agenda.semana') }}"
                           class="sidebar-nav-item {{ request()->routeIs('painel.agenda.semana') ? 'active' : '' }}"
                           @click="sidebarOpen = false">
                            Semana
                        </a>
                        <a href="{{ route('painel.agenda.lista') }}"
                           class="sidebar-nav-item {{ request()->routeIs('painel.agenda.lista') ? 'active' : '' }}"
                           @click="sidebarOpen = false">
                            Lista
                        </a>
                    </div>
                </div>
            </div>

            {{-- Pacientes --}}
            <a href="{{ route('painel.pacientes.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('painel.pacientes.*') ? 'active' : '' }}"
               @click="sidebarOpen = false">
                <i class="bi bi-people"></i>
                <span>Pacientes</span>
            </a>

            {{-- Financeiro --}}
            <a href="{{ route('painel.financeiro.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('painel.financeiro.*') ? 'active' : '' }}"
               @click="sidebarOpen = false">
                <i class="bi bi-cash-coin"></i>
                <span>Financeiro</span>
            </a>

            {{-- Automações --}}
            <a href="{{ route('painel.automacoes.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('painel.automacoes.*') ? 'active' : '' }}"
               @click="sidebarOpen = false">
                <i class="bi bi-chat-dots"></i>
                <span>Automações</span>
            </a>

            {{-- Minha Página --}}
            <a href="{{ route('painel.pagina.editor') }}"
               class="sidebar-nav-item {{ request()->routeIs('painel.pagina.*') ? 'active' : '' }}"
               @click="sidebarOpen = false">
                <i class="bi bi-globe2"></i>
                <span>Minha Página</span>
            </a>

            <div class="section-separator" style="margin: 1rem 1.25rem 0; color: rgba(255,255,255,0.25);">
                <div style="flex:1;height:1px;background:rgba(255,255,255,0.1);"></div>
            </div>

            {{-- Configurações --}}
            <a href="{{ route('painel.configuracoes.index') }}"
               class="sidebar-nav-item {{ request()->routeIs('painel.configuracoes.*') ? 'active' : '' }}"
               @click="sidebarOpen = false">
                <i class="bi bi-gear"></i>
                <span>Configurações</span>
            </a>

        </nav>

        {{-- Rodapé da sidebar: info do profissional --}}
        <div class="sabenta-sidebar__footer">
            <div class="sidebar-user">
                <div class="sidebar-user__avatar">AS</div>
                <div class="sidebar-user__info">
                    <div class="sidebar-user__name">Dra. Ana Souza</div>
                    <div class="sidebar-user__role">Psicóloga Clínica</div>
                </div>
                <a href="{{ route('login') }}"
                   class="topbar-action"
                   style="color: rgba(255,255,255,0.45); width:32px; height:32px;"
                   title="Sair da conta">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>

    </aside>

    {{-- Overlay mobile --}}
    <div class="sidebar-overlay"
         :class="{ 'show': sidebarOpen }"
         @click="sidebarOpen = false"></div>

    {{-- =====================================================
         ÁREA DE CONTEÚDO
         ===================================================== --}}
    <div class="sabenta-content">

        {{-- Topbar --}}
        <header class="sabenta-topbar">

            {{-- Hamburguer (mobile) --}}
            <button type="button"
                    class="topbar-action d-lg-none"
                    @click="sidebarOpen = !sidebarOpen"
                    aria-label="Abrir menu">
                <i class="bi bi-list" style="font-size: 1.25rem;"></i>
            </button>

            {{-- Breadcrumb / título da página --}}
            <div class="flex-grow-1">
                @yield('breadcrumb')
            </div>

            {{-- Ações do lado direito --}}
            <div class="d-flex align-items-center gap-2">

                {{-- Notificações --}}
                <a href="#" class="topbar-action position-relative" title="Notificações">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute"
                          style="top:7px;right:7px;width:7px;height:7px;border-radius:50%;background:#DC2626;border:1.5px solid white;"></span>
                </a>

                <div class="topbar-divider"></div>

                {{-- Dropdown do profissional --}}
                <div class="dropdown">
                    <button type="button"
                            class="btn p-0 border-0 bg-transparent d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <div class="topbar-avatar">AS</div>
                        <div class="d-none d-md-flex flex-column align-items-start" style="line-height: 1.2;">
                            <span style="font-family: var(--font-heading); font-size: 0.8125rem; font-weight: 600; color: var(--sabenta-text);">
                                Dra. Ana Souza
                            </span>
                            <span style="font-size: 0.7rem; color: var(--sabenta-text-muted);">Psicóloga Clínica</span>
                        </div>
                        <i class="bi bi-chevron-down d-none d-md-block"
                           style="font-size: 0.65rem; color: var(--sabenta-text-muted);"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('painel.configuracoes.index') }}">
                                <i class="bi bi-person-circle"></i> Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('painel.configuracoes.index') }}">
                                <i class="bi bi-gear"></i> Configurações
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('painel.pagina.editor') }}">
                                <i class="bi bi-globe2"></i> Minha Página
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-right"></i> Sair da conta
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </header>

        {{-- Conteúdo principal --}}
        <main class="sabenta-main">
            @yield('content')
        </main>

        {{-- Footer interno --}}
        <footer class="sabenta-footer">
            <span>v1.0.0 &mdash; Sabenta</span>
            <a href="#">Suporte</a>
        </footer>

    </div>
</div>

@stack('modals')
@stack('scripts')

</body>
</html>
