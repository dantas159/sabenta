@extends('layouts.app')

@section('title', 'Painel')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Painel</li>
        </ol>
    </nav>
@endsection

@section('content')

{{-- Page Header --}}
<x-page-header
    titulo="Painel"
    :sub="'Sábado, ' . \Carbon\Carbon::now()->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY')"
/>

{{-- ============================================
     Stat Cards
     ============================================ --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <x-stat-card
            titulo="Atendimentos hoje"
            valor="6"
            icone="calendar-check"
            cor="primary"
            delta="+2 vs ontem"
            :deltaUp="true"
        />
    </div>
    <div class="col-6 col-xl-3">
        <x-stat-card
            titulo="Atendimentos esta semana"
            valor="23"
            icone="calendar-week"
            cor="purple"
            delta="+4 vs semana passada"
            :deltaUp="true"
        />
    </div>
    <div class="col-6 col-xl-3">
        <x-stat-card
            titulo="Receita do mês"
            valor="R$ 4.200"
            icone="cash-coin"
            cor="success"
            delta="+12% vs mês anterior"
            :deltaUp="true"
        />
    </div>
    <div class="col-6 col-xl-3">
        <x-stat-card
            titulo="Clientes ativos"
            valor="18"
            icone="people"
            cor="warning"
            delta="-1 este mês"
            :deltaUp="false"
        />
    </div>
</div>

{{-- ============================================
     Atendimentos de hoje + Alertas
     ============================================ --}}
<div class="row g-3">

    {{-- Atendimentos de hoje --}}
    <div class="col-lg-8">
        <div class="sabenta-card h-100">
            <div class="card-header">
                <span class="card-header-title">
                    <i class="bi bi-calendar-day me-2" style="color: var(--sabenta-primary);"></i>
                    Atendimentos de hoje
                </span>
                <div class="d-flex align-items-center gap-2">
                    <span class="sabenta-badge badge-confirmado" style="font-size: 0.7rem;">
                        6 atendimentos
                    </span>
                    <a href="{{ route('painel.agenda.dia') }}"
                       class="btn btn-sm btn-outline-secondary"
                       style="font-size: 0.8rem; padding: 0.3rem 0.75rem;">
                        Ver agenda
                    </a>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="sabenta-table">
                    <thead>
                        <tr>
                            <th>Horário</th>
                            <th>Cliente</th>
                            <th>Serviço</th>
                            <th>Duração</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sessoes = [
                                ['hora' => '08:00', 'paciente' => 'Mariana Costa',    'iniciais' => 'MC', 'servico' => 'Atendimento Individual', 'duracao' => '50 min', 'status' => 'realizado', 'cor' => '#1D9E75'],
                                ['hora' => '09:00', 'paciente' => 'Pedro Alves',      'iniciais' => 'PA', 'servico' => 'Avaliação Inicial',   'duracao' => '60 min', 'status' => 'realizado', 'cor' => '#1D9E75'],
                                ['hora' => '10:30', 'paciente' => 'Fernanda Lima',    'iniciais' => 'FL', 'servico' => 'Atendimento Individual', 'duracao' => '50 min', 'status' => 'confirmado','cor' => '#1E5BAD'],
                                ['hora' => '14:00', 'paciente' => 'Lucas Mendes',     'iniciais' => 'LM', 'servico' => 'Atendimento Individual', 'duracao' => '50 min', 'status' => 'confirmado','cor' => '#1E5BAD'],
                                ['hora' => '15:30', 'paciente' => 'Juliana Ferreira', 'iniciais' => 'JF', 'servico' => 'Atendimento em Dupla',  'duracao' => '90 min', 'status' => 'pendente',  'cor' => '#D97706'],
                                ['hora' => '17:30', 'paciente' => 'Rafael Santos',    'iniciais' => 'RS', 'servico' => 'Atendimento Individual', 'duracao' => '50 min', 'status' => 'faltou',   'cor' => '#DC2626'],
                            ];
                        @endphp

                        @foreach($sessoes as $s)
                        <tr>
                            <td>
                                <span style="font-family: var(--font-heading); font-weight: 600; font-size: 0.875rem;">
                                    {{ $s['hora'] }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="sabenta-avatar avatar-sm"
                                         style="background: {{ $s['cor'] }}; opacity: 0.85;">
                                        {{ $s['iniciais'] }}
                                    </div>
                                    <span style="font-weight: 500; font-size: 0.875rem;">
                                        {{ $s['paciente'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 0.8375rem; color: var(--sabenta-text-muted);">
                                    {{ $s['servico'] }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.8375rem;">{{ $s['duracao'] }}</span>
                            </td>
                            <td>
                                <x-badge-status :status="$s['status']" />
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-end">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            style="padding: 0.25rem 0.6rem; font-size: 0.8rem;"
                                            title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer d-flex align-items-center justify-content-between">
                <span>Próximo atendimento às <strong>14:00</strong> — Lucas Mendes</span>
                <a href="{{ route('painel.agenda.dia') }}" class="text-sabenta" style="font-size: 0.8125rem; font-weight: 600;">
                    Ver agenda completa <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    <div class="col-lg-4">
        <div class="sabenta-card h-100">
            <div class="card-header">
                <span class="card-header-title">
                    <i class="bi bi-bell-fill me-2" style="color: var(--status-pendente);"></i>
                    Alertas
                </span>
                <span class="sabenta-badge badge-faltou" style="font-size: 0.7rem;">3</span>
            </div>
            <div class="card-body" style="padding: 1rem 1.25rem;">

                <x-alert-card
                    tipo="danger"
                    titulo="Rafael Santos faltou"
                    descricao="Atendimento das 17:30 sem confirmação de presença."
                    href="{{ route('painel.pacientes.show') }}"
                />

                <x-alert-card
                    tipo="warning"
                    titulo="Fernanda Lima sem próximo atendimento"
                    descricao="Último atendimento há 14 dias. Nenhum futuro agendado."
                    href="{{ route('painel.pacientes.show') }}"
                />

                <x-alert-card
                    tipo="warning"
                    titulo="3 confirmações pendentes"
                    descricao="Lucas, Juliana e Pedro ainda não confirmaram amanhã."
                    href="{{ route('painel.agenda.lista') }}"
                />

            </div>
            <div class="card-footer">
                <a href="{{ route('painel.automacoes.index') }}" class="text-sabenta"
                   style="font-size: 0.8125rem; font-weight: 600;">
                    <i class="bi bi-chat-dots me-1"></i> Configurar automações
                </a>
            </div>
        </div>
    </div>

</div>

{{-- ============================================
     Linha de ações rápidas
     ============================================ --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="sabenta-card">
            <div class="card-body d-flex align-items-center gap-3 flex-wrap" style="padding: 1rem 1.25rem;">
                <span style="font-family: var(--font-heading); font-size: 0.8125rem; font-weight: 600; color: var(--sabenta-text-muted);">
                    Ações rápidas:
                </span>
                <a href="{{ route('painel.agenda.dia') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Novo atendimento
                </a>
                <a href="{{ route('painel.pacientes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-person-plus"></i> Novo cliente
                </a>
                <a href="{{ route('painel.financeiro.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-cash"></i> Registrar pagamento
                </a>
                <a href="{{ route('painel.pagina.editor') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-globe2"></i> Ver minha página
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
