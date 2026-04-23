@extends('layouts.app')
@section('title', 'Agenda — Lista')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Agenda — Lista</li>
</ol></nav>
@endsection
@section('content')

<x-page-header titulo="Agenda — Lista" botao="Novo atendimento" botaoIcone="plus-lg" botaoTarget="#modalNovoAtendimentoLista" botaoHref="#" />

{{-- Filtros --}}
<div class="sabenta-card mb-3">
<div class="card-body" style="padding:1rem 1.25rem;">
<div class="row g-2 align-items-end">
    <div class="col-md-2">
        <label class="form-label">Data início</label>
        <input type="date" class="form-control" value="2025-04-01">
    </div>
    <div class="col-md-2">
        <label class="form-label">Data fim</label>
        <input type="date" class="form-control" value="2025-04-30">
    </div>
    <div class="col-md-3">
        <label class="form-label">Cliente</label>
        <select class="form-select">
            <option value="">Todos</option>
            <option>Mariana Costa</option><option>Pedro Alves</option>
            <option>Fernanda Lima</option><option>Lucas Mendes</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Status</label>
        <select class="form-select">
            <option value="">Todos</option>
            <option>Confirmado</option><option>Pendente</option>
            <option>Realizado</option><option>Faltou</option><option>Cancelado</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Serviço</label>
        <select class="form-select">
            <option value="">Todos</option>
            <option>Atendimento Individual</option><option>Atendimento em Dupla</option>
            <option>Avaliação Inicial</option>
        </select>
    </div>
    <div class="col-md-1">
        <button class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
    </div>
</div>
</div>
</div>

{{-- Tabela --}}
<div class="sabenta-card">
<div class="card-header">
    <span class="card-header-title">Atendimentos — Abril 2025</span>
    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i>Exportar CSV</button>
</div>
<div style="overflow-x:auto;">
@php
$sessoes = [
    ['2025-04-07','09:00','Mariana Costa','Atendimento Individual','50 min','confirmado','R$ 200','Pix'],
    ['2025-04-07','11:00','Pedro Alves','Avaliação Inicial','60 min','realizado','R$ 350','Transferência'],
    ['2025-04-08','14:00','Fernanda Lima','Atendimento Individual','50 min','faltou','R$ 200','—'],
    ['2025-04-09','09:30','Lucas Mendes','Atendimento Individual','50 min','confirmado','R$ 200','Pix'],
    ['2025-04-10','10:00','Juliana Ferreira','Atendimento em Dupla','90 min','pendente','R$ 400','—'],
    ['2025-04-14','09:00','Mariana Costa','Atendimento Individual','50 min','realizado','R$ 200','Pix'],
    ['2025-04-14','14:30','Rafael Santos','Atendimento Individual','50 min','cancelado','R$ 200','—'],
    ['2025-04-15','11:00','Pedro Alves','Avaliação Inicial','60 min','confirmado','R$ 350','—'],
    ['2025-04-16','09:30','Lucas Mendes','Atendimento Individual','50 min','confirmado','R$ 200','—'],
    ['2025-04-17','09:00','Mariana Costa','Atendimento Individual','50 min','confirmado','R$ 200','—'],
];
@endphp
<table class="sabenta-table">
<thead><tr>
    <th>Data</th><th>Horário</th><th>Cliente</th><th>Serviço</th><th>Duração</th><th>Status</th><th>Valor</th><th>Pagamento</th><th></th>
</tr></thead>
<tbody>
@foreach($sessoes as $s)
<tr>
    <td style="font-family:var(--font-heading);font-weight:600;font-size:.875rem;">{{ \Carbon\Carbon::parse($s[0])->locale('pt_BR')->isoFormat('D MMM') }}</td>
    <td style="font-weight:600;">{{ $s[1] }}</td>
    <td>
        <div class="d-flex align-items-center gap-2">
            <div class="sabenta-avatar avatar-sm">{{ substr($s[2],0,1).substr(strpos($s[2],' ')!==false?$s[2][strpos($s[2],' ')+1]:'',0,1) }}</div>
            <span style="font-weight:500;font-size:.875rem;">{{ $s[2] }}</span>
        </div>
    </td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $s[3] }}</td>
    <td style="font-size:.8375rem;">{{ $s[4] }}</td>
    <td><x-badge-status :status="$s[5]" /></td>
    <td style="font-weight:600;font-size:.875rem;">{{ $s[6] }}</td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $s[7] }}</td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" style="padding:.25rem .5rem;">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i>Ver detalhes</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil"></i>Editar</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-cash"></i>Registrar pagamento</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash"></i>Excluir</a></li>
            </ul>
        </div>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="card-footer d-flex align-items-center justify-content-between">
    <span style="font-size:.8125rem;">Exibindo 10 de 47 atendimentos</span>
    <nav><ul class="pagination pagination-sm mb-0">
        <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">4</a></li>
        <li class="page-item"><a class="page-link" href="#">5</a></li>
        <li class="page-item"><a class="page-link" href="#">»</a></li>
    </ul></nav>
</div>
</div>

{{-- Modal novo atendimento --}}
<div class="modal fade" id="modalNovoAtendimentoLista" tabindex="-1">
<div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title"><i class="bi bi-calendar-plus me-2" style="color:var(--sabenta-primary);"></i>Novo atendimento</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
<div class="row g-3">
    <div class="col-12"><label class="form-label">Cliente</label>
    <select class="form-select"><option>Selecionar...</option><option>Mariana Costa</option><option>Pedro Alves</option></select></div>
    <div class="col-12"><label class="form-label">Serviço</label>
    <select class="form-select"><option>Atendimento Individual</option><option>Avaliação Inicial</option></select></div>
    <div class="col-6"><label class="form-label">Data</label><input type="date" class="form-control"></div>
    <div class="col-6"><label class="form-label">Horário</label><input type="time" class="form-control"></div>
    <div class="col-6"><label class="form-label">Duração</label>
    <select class="form-select"><option>30 min</option><option>45 min</option><option selected>50 min</option><option>60 min</option></select></div>
    <div class="col-6"><label class="form-label">Valor</label>
    <div class="input-group"><span class="input-group-text">R$</span><input type="number" class="form-control" placeholder="200"></div></div>
</div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar atendimento</button>
</div>
</div></div></div>

@endsection
