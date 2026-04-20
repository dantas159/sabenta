@extends('layouts.app')
@section('title', 'Pacientes')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Pacientes</li>
</ol></nav>
@endsection
@section('content')

<x-page-header titulo="Pacientes" botao="Novo paciente" botaoIcone="person-plus" botaoTarget="#modalNovoPaciente" botaoHref="#" />

{{-- Busca e filtros --}}
<div class="sabenta-card mb-3">
<div class="card-body" style="padding:1rem 1.25rem;">
<div class="row g-2 align-items-end">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Buscar por nome, e-mail ou WhatsApp...">
        </div>
    </div>
    <div class="col-md-3">
        <select class="form-select">
            <option value="">Todos os pacientes</option>
            <option>Ativos</option>
            <option>Inativos</option>
            <option>Novos (este mês)</option>
        </select>
    </div>
    <div class="col-md-3">
        <select class="form-select">
            <option value="">Ordenar por...</option>
            <option>Nome A–Z</option>
            <option>Última sessão</option>
            <option>Próxima sessão</option>
        </select>
    </div>
</div>
</div>
</div>

{{-- Tabela --}}
<div class="sabenta-card">
<div class="card-header">
    <span class="card-header-title">18 pacientes ativos</span>
    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i>Exportar</button>
</div>
<div style="overflow-x:auto;">
@php
$pacientes = [
    ['Mariana Costa','(11) 99211-4455','12','10/04/2025','17/04/2025','ativo'],
    ['Pedro Alves','(21) 98765-3210','8','07/04/2025','14/04/2025','ativo'],
    ['Fernanda Lima','(11) 91234-5678','24','03/04/2025','17/04/2025','ativo'],
    ['Lucas Mendes','(11) 97654-3321','6','09/04/2025','16/04/2025','ativo'],
    ['Juliana Ferreira','(31) 99988-7766','15','01/04/2025','21/04/2025','ativo'],
    ['Rafael Santos','(11) 95555-4444','3','17/04/2025','—','ativo'],
    ['Camila Rodrigues','(11) 93333-2222','19','28/03/2025','24/04/2025','ativo'],
    ['Thiago Oliveira','(21) 91111-0000','7','20/03/2025','—','inativo'],
];
@endphp
<table class="sabenta-table">
<thead><tr>
    <th>Paciente</th><th>WhatsApp</th><th>Total sessões</th><th>Última sessão</th><th>Próxima sessão</th><th>Status</th><th></th>
</tr></thead>
<tbody>
@foreach($pacientes as $p)
<tr>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div class="sabenta-avatar avatar-sm" style="background:{{ ['var(--sabenta-primary)','#8B5CF6','var(--status-confirmado)','var(--status-pendente)'][array_rand(['a','b','c','d'])] }};">
                {{ substr($p[0],0,1) }}{{ substr(strstr($p[0],' '),1,1) }}
            </div>
            <a href="{{ route('painel.pacientes.show') }}" style="font-weight:600;font-size:.875rem;color:var(--sabenta-text);">{{ $p[0] }}</a>
        </div>
    </td>
    <td style="font-size:.875rem;color:var(--sabenta-text-muted);">{{ $p[1] }}</td>
    <td style="font-weight:600;text-align:center;">{{ $p[2] }}</td>
    <td style="font-size:.875rem;color:var(--sabenta-text-muted);">{{ $p[3] }}</td>
    <td style="font-size:.875rem;">{{ $p[4] === '—' ? '<span style="color:var(--sabenta-text-muted);">—</span>' : $p[4] }}</td>
    <td><span class="sabenta-badge {{ $p[5] === 'ativo' ? 'badge-confirmado' : 'badge-cancelado' }}">{{ ucfirst($p[5]) }}</span></td>
    <td>
        <div class="d-flex gap-1">
            <a href="{{ route('painel.pacientes.show') }}" class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;" title="Ver perfil"><i class="bi bi-person"></i></a>
            <a href="{{ route('painel.agenda.dia') }}" class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;" title="Agendar sessão"><i class="bi bi-calendar-plus"></i></a>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('painel.pacientes.notas') }}"><i class="bi bi-journal-text"></i>Anotações</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil"></i>Editar dados</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i>Excluir</a></li>
                </ul>
            </div>
        </div>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="card-footer d-flex align-items-center justify-content-between">
    <span style="font-size:.8125rem;">Exibindo 8 de 18 pacientes</span>
    <nav><ul class="pagination pagination-sm mb-0">
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
    </ul></nav>
</div>
</div>

{{-- Modal 4-A: Novo paciente --}}
<div class="modal fade" id="modalNovoPaciente" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-person-plus me-2" style="color:var(--sabenta-primary);"></i>Novo paciente</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form method="POST" action="#">@csrf
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Nome completo</label>
        <input type="text" class="form-control" placeholder="Nome do paciente" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">WhatsApp</label>
        <input type="tel" class="form-control" placeholder="(11) 99999-9999" data-mask="phone">
    </div>
    <div class="col-md-6">
        <label class="form-label">E-mail</label>
        <input type="email" class="form-control" placeholder="email@exemplo.com">
    </div>
    <div class="col-md-6">
        <label class="form-label">Data de nascimento</label>
        <input type="date" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Como chegou até mim?</label>
        <select class="form-select">
            <option value="">Selecionar...</option>
            <option>Indicação de paciente</option>
            <option>Google/Internet</option>
            <option>Redes sociais</option>
            <option>Plano de saúde</option>
            <option>Outro</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Observações iniciais</label>
        <textarea class="form-control" rows="3" placeholder="Queixas, histórico breve, observações relevantes..."></textarea>
        <div class="form-text"><i class="bi bi-lock-fill me-1"></i>Visível apenas para você</div>
    </div>
</div>
</form>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Cadastrar paciente</button>
</div>
</div></div></div>

@endsection
