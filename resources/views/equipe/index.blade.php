@extends('layouts.app')
@section('title', 'Equipe')

@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
    <li class="breadcrumb-item active">Equipe</li>
</ol></nav>
@endsection

@section('content')

<x-page-header
    titulo="Equipe"
    sub="Gerencie os membros e permissões da sua clínica"
    botao="Convidar membro"
    botaoIcone="person-plus"
    botaoHref="#"
    botaoTarget="#modalConvidar"
/>

{{-- Stats rápidos --}}
@php
$membros = [
    ['nome' => 'Dra. Ana Souza',     'email' => 'ana.souza@psicologia.com.br',   'role' => 'admin',         'status' => 'ativo',    'ultimo' => 'Agora',         'iniciais' => 'AS', 'cor' => '#3b82f6'],
    ['nome' => 'Dr. Carlos Mendes',  'email' => 'carlos.mendes@psicologia.com',  'role' => 'profissional',  'status' => 'ativo',    'ultimo' => 'Há 2 horas',    'iniciais' => 'CM', 'cor' => '#8b5cf6'],
    ['nome' => 'Dra. Juliana Lima',  'email' => 'juliana.lima@clinica.com.br',   'role' => 'profissional',  'status' => 'ativo',    'ultimo' => 'Há 1 dia',      'iniciais' => 'JL', 'cor' => '#10b981'],
    ['nome' => 'Fernanda Rocha',     'email' => 'fernanda.rocha@clinica.com.br', 'role' => 'recepcionista', 'status' => 'ativo',    'ultimo' => 'Há 3 horas',    'iniciais' => 'FR', 'cor' => '#f59e0b'],
    ['nome' => 'Roberto Alves',      'email' => 'roberto.alves@clinica.com.br',  'role' => 'recepcionista', 'status' => 'inativo',  'ultimo' => 'Há 5 dias',     'iniciais' => 'RA', 'cor' => '#94a3b8'],
];

$pendentes = [
    ['email' => 'novo.psi@gmail.com',       'role' => 'profissional',  'enviado' => '18/04/2026'],
    ['email' => 'recep.silva@gmail.com',    'role' => 'recepcionista', 'enviado' => '19/04/2026'],
];

$roleLabels = [
    'admin'         => ['label' => 'Administrador', 'class' => 'badge-confirmado'],
    'profissional'  => ['label' => 'Profissional',  'class' => 'badge-realizado'],
    'recepcionista' => ['label' => 'Recepcionista', 'class' => 'badge-pendente'],
];
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="sabenta-card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:900;font-family:var(--font-heading);color:var(--sabenta-primary);">{{ count($membros) }}</div>
            <div style="font-size:0.78rem;color:var(--sabenta-text-muted);">Membros totais</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sabenta-card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:900;font-family:var(--font-heading);color:#8b5cf6;">{{ collect($membros)->where('role','profissional')->count() }}</div>
            <div style="font-size:0.78rem;color:var(--sabenta-text-muted);">Profissionais</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sabenta-card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:900;font-family:var(--font-heading);color:#f59e0b;">{{ collect($membros)->where('role','recepcionista')->count() }}</div>
            <div style="font-size:0.78rem;color:var(--sabenta-text-muted);">Recepcionistas</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sabenta-card p-3 text-center">
            <div style="font-size:1.75rem;font-weight:900;font-family:var(--font-heading);color:var(--sabenta-warning, #f59e0b);">{{ count($pendentes) }}</div>
            <div style="font-size:0.78rem;color:var(--sabenta-text-muted);">Convites pendentes</div>
        </div>
    </div>
</div>

{{-- Tabela de membros --}}
<div class="sabenta-card mb-4">
    <div class="card-header">
        <span class="card-header-title">Membros ativos</span>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" style="width:auto;">
                <option value="">Todos os perfis</option>
                <option>Administrador</option>
                <option>Profissional</option>
                <option>Recepcionista</option>
            </select>
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="sabenta-table">
            <thead>
                <tr>
                    <th>Membro</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th>Último acesso</th>
                    <th>Sessões (mês)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($membros as $m)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="sabenta-avatar avatar-sm"
                                 style="background:{{ $m['cor'] }}22;color:{{ $m['cor'] }};font-size:0.75rem;font-weight:700;">
                                {{ $m['iniciais'] }}
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:0.875rem;">{{ $m['nome'] }}</div>
                                <div style="font-size:0.775rem;color:var(--sabenta-text-muted);">{{ $m['email'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="sabenta-badge {{ $roleLabels[$m['role']]['class'] }}" style="font-size:0.7rem;">
                            {{ $roleLabels[$m['role']]['label'] }}
                        </span>
                    </td>
                    <td>
                        @if($m['status'] === 'ativo')
                            <span class="d-flex align-items-center gap-1" style="font-size:0.8125rem;color:var(--sabenta-success);">
                                <span style="width:6px;height:6px;border-radius:50%;background:var(--sabenta-success);display:inline-block;"></span>
                                Ativo
                            </span>
                        @else
                            <span class="d-flex align-items-center gap-1" style="font-size:0.8125rem;color:var(--sabenta-text-muted);">
                                <span style="width:6px;height:6px;border-radius:50%;background:var(--sabenta-border);display:inline-block;"></span>
                                Inativo
                            </span>
                        @endif
                    </td>
                    <td style="font-size:0.8125rem;color:var(--sabenta-text-muted);">{{ $m['ultimo'] }}</td>
                    <td style="font-weight:600;font-size:0.875rem;">{{ rand(8,24) }}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown"
                                    style="padding:.25rem .5rem;"
                                    {{ $m['role'] === 'admin' ? 'disabled' : '' }}>
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"
                                       data-bs-toggle="modal" data-bs-target="#modalEditarRole"
                                       data-nome="{{ $m['nome'] }}"
                                       data-role="{{ $m['role'] }}">
                                    <i class="bi bi-person-gear"></i> Editar perfil
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-person-lines-fill"></i> Ver agenda
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#"
                                       @if($m['status'] === 'ativo') style="color:var(--sabenta-warning);" @endif>
                                    <i class="bi bi-{{ $m['status'] === 'ativo' ? 'pause-circle' : 'play-circle' }}"></i>
                                    {{ $m['status'] === 'ativo' ? 'Desativar acesso' : 'Reativar acesso' }}
                                </a></li>
                                <li><a class="dropdown-item text-danger" href="#">
                                    <i class="bi bi-person-x"></i> Remover da clínica
                                </a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Convites pendentes --}}
@if(count($pendentes) > 0)
<div class="sabenta-card mb-4">
    <div class="card-header">
        <span class="card-header-title">Convites pendentes</span>
        <span class="sabenta-badge badge-pendente" style="font-size:0.7rem;">{{ count($pendentes) }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sabenta-table">
            <thead>
                <tr><th>E-mail</th><th>Perfil</th><th>Enviado em</th><th>Expira em</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($pendentes as $p)
                <tr>
                    <td style="font-size:0.875rem;font-weight:500;">{{ $p['email'] }}</td>
                    <td>
                        <span class="sabenta-badge {{ $roleLabels[$p['role']]['class'] }}" style="font-size:0.7rem;">
                            {{ $roleLabels[$p['role']]['label'] }}
                        </span>
                    </td>
                    <td style="font-size:0.8125rem;color:var(--sabenta-text-muted);">{{ $p['enviado'] }}</td>
                    <td style="font-size:0.8125rem;color:var(--sabenta-text-muted);">em 7 dias</td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;" title="Reenviar">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" style="padding:.25rem .55rem;" title="Cancelar convite">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Modal: Convidar membro --}}
<div class="modal fade" id="modalConvidar" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">
            <i class="bi bi-person-plus me-2" style="color:var(--sabenta-primary);"></i>Convidar membro
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" x-data="{ role: 'profissional' }">
        <div class="mb-3">
            <label class="form-label">E-mail do convidado *</label>
            <input type="email" class="form-control" placeholder="nome@clinica.com.br">
        </div>

        <div class="mb-3">
            <label class="form-label">Perfil de acesso *</label>
            <div class="d-flex flex-column gap-2 mt-1">

                <label class="d-flex align-items-start gap-3 p-3 rounded-3 border"
                       :class="role === 'profissional' ? 'border-primary bg-primary-subtle' : 'border-light'"
                       style="cursor:pointer;">
                    <input type="radio" name="role" value="profissional" x-model="role" class="mt-1">
                    <div>
                        <div style="font-weight:700;font-size:0.875rem;font-family:var(--font-heading);">Profissional</div>
                        <div style="font-size:0.78rem;color:var(--sabenta-text-muted);line-height:1.5;">
                            Acessa sua própria agenda e seus clientes. Não vê dados de outros membros da equipe.
                        </div>
                    </div>
                </label>

                <label class="d-flex align-items-start gap-3 p-3 rounded-3 border"
                       :class="role === 'recepcionista' ? 'border-primary bg-primary-subtle' : 'border-light'"
                       style="cursor:pointer;">
                    <input type="radio" name="role" value="recepcionista" x-model="role" class="mt-1">
                    <div>
                        <div style="font-weight:700;font-size:0.875rem;font-family:var(--font-heading);">Recepcionista</div>
                        <div style="font-size:0.78rem;color:var(--sabenta-text-muted);line-height:1.5;">
                            Acessa todas as agendas e pode cadastrar clientes. Sem acesso ao financeiro ou configurações.
                        </div>
                    </div>
                </label>

                <label class="d-flex align-items-start gap-3 p-3 rounded-3 border"
                       :class="role === 'admin' ? 'border-primary bg-primary-subtle' : 'border-light'"
                       style="cursor:pointer;">
                    <input type="radio" name="role" value="admin" x-model="role" class="mt-1">
                    <div>
                        <div style="font-weight:700;font-size:0.875rem;font-family:var(--font-heading);">
                            Administrador
                            <span style="font-size:0.65rem;background:#fee2e2;color:#dc2626;border-radius:6px;padding:0.1rem 0.4rem;vertical-align:middle;margin-left:0.4rem;">Acesso total</span>
                        </div>
                        <div style="font-size:0.78rem;color:var(--sabenta-text-muted);line-height:1.5;">
                            Acesso completo à clínica: agenda de todos, financeiro, equipe, configurações e assinatura.
                        </div>
                    </div>
                </label>

            </div>
        </div>

        <div class="mb-1">
            <label class="form-label">Mensagem personalizada <span style="font-size:0.75rem;color:var(--sabenta-text-muted);">(opcional)</span></label>
            <textarea class="form-control" rows="2" placeholder="Ex: Olá! Estou te convidando para integrar nossa equipe na Clínica Sabenta."></textarea>
        </div>

        <div class="mt-3 p-3 rounded-3" style="background:var(--sabenta-primary-light);font-size:0.78rem;color:var(--sabenta-primary);">
            <i class="bi bi-info-circle me-1"></i>
            O convidado receberá um e-mail com um link de acesso válido por <strong>7 dias</strong>.
            Ele precisará criar uma senha na primeira vez que acessar.
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">
            <i class="bi bi-send me-1"></i>Enviar convite
        </button>
    </div>
</div>
</div>
</div>

{{-- Modal: Editar perfil do membro --}}
<div class="modal fade" id="modalEditarRole" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-sm">
<div class="modal-content" x-data="{ role: 'profissional' }">
    <div class="modal-header">
        <h5 class="modal-title">
            <i class="bi bi-person-gear me-2" style="color:var(--sabenta-primary);"></i>Editar perfil
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <p style="font-size:0.875rem;font-weight:600;margin-bottom:1rem;" id="editarRoleNome">Dr. Carlos Mendes</p>
        <label class="form-label">Perfil de acesso</label>
        <select class="form-select" x-model="role">
            <option value="profissional">Profissional</option>
            <option value="recepcionista">Recepcionista</option>
            <option value="admin">Administrador</option>
        </select>
        <template x-if="role === 'admin'">
            <div class="mt-2 p-2 rounded-3" style="background:#fee2e2;font-size:0.78rem;color:#dc2626;" x-transition>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Administradores têm acesso total à clínica, incluindo financeiro e assinatura.
            </div>
        </template>
    </div>
    <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary btn-sm">Salvar alteração</button>
    </div>
</div>
</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('modalEditarRole');
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget;
            if (btn) {
                document.getElementById('editarRoleNome').textContent = btn.dataset.nome || '';
            }
        });
    }
});
</script>
@endpush

@endsection
