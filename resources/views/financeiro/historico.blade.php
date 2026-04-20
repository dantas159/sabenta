@extends('layouts.app')
@section('title', 'Histórico Financeiro')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item"><a href="{{ route('painel.financeiro.index') }}">Financeiro</a></li>
<li class="breadcrumb-item active">Histórico</li>
</ol></nav>
@endsection
@section('content')

<x-page-header titulo="Histórico financeiro" sub="Registro completo de recebimentos" />

{{-- Filtros --}}
<div class="sabenta-card mb-3">
<div class="card-body" style="padding:1rem 1.25rem;">
<div class="row g-2 align-items-end">
    <div class="col-md-2"><label class="form-label">Data início</label><input type="date" class="form-control" value="2025-01-01"></div>
    <div class="col-md-2"><label class="form-label">Data fim</label><input type="date" class="form-control" value="2025-04-17"></div>
    <div class="col-md-3"><label class="form-label">Paciente</label>
    <select class="form-select"><option value="">Todos</option><option>Mariana Costa</option><option>Pedro Alves</option></select></div>
    <div class="col-md-2"><label class="form-label">Forma pagamento</label>
    <select class="form-select"><option value="">Todas</option><option>Pix</option><option>Dinheiro</option><option>Transferência</option></select></div>
    <div class="col-md-2"><label class="form-label">Status</label>
    <select class="form-select"><option value="">Todos</option><option>Pago</option><option>Pendente</option></select></div>
    <div class="col-md-1"><button class="btn btn-primary w-100"><i class="bi bi-search"></i></button></div>
</div>
</div>
</div>

<div class="sabenta-card">
<div class="card-header">
    <span class="card-header-title">Registros financeiros</span>
    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i>Exportar CSV</button>
</div>
<div style="overflow-x:auto;">
@php
$hist = [
    ['07/04/2025','07/04/2025','Mariana Costa','Terapia Individual','R$ 200','Pix','pago'],
    ['07/04/2025','07/04/2025','Pedro Alves','Avaliação Psicológica','R$ 350','Transferência','pago'],
    ['09/04/2025','09/04/2025','Lucas Mendes','Terapia Individual','R$ 200','Pix','pago'],
    ['14/04/2025','14/04/2025','Mariana Costa','Terapia Individual','R$ 200','Pix','pago'],
    ['08/04/2025','—','Fernanda Lima','Terapia Individual','R$ 200','—','pendente'],
    ['10/04/2025','—','Juliana Ferreira','Terapia de Casal','R$ 400','—','pendente'],
    ['15/04/2025','—','Pedro Alves','Avaliação Psicológica','R$ 350','—','pendente'],
    ['03/04/2025','—','Fernanda Lima','Terapia Individual','R$ 200','—','pendente'],
    ['28/03/2025','28/03/2025','Mariana Costa','Terapia Individual','R$ 200','Pix','pago'],
    ['27/03/2025','27/03/2025','Lucas Mendes','Terapia Individual','R$ 200','Dinheiro','pago'],
];
@endphp
<table class="sabenta-table">
<thead><tr><th>Data sessão</th><th>Data pagamento</th><th>Paciente</th><th>Serviço</th><th>Valor</th><th>Forma</th><th>Status</th><th></th></tr></thead>
<tbody>
@foreach($hist as $h)
<tr>
    <td style="font-weight:600;font-size:.875rem;">{{ $h[0] }}</td>
    <td style="font-size:.8375rem;color:{{ $h[1]==='—' ? 'var(--sabenta-text-muted)' : 'var(--sabenta-text)' }};">{{ $h[1] }}</td>
    <td style="font-weight:500;font-size:.875rem;">{{ $h[2] }}</td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $h[3] }}</td>
    <td style="font-weight:700;">{{ $h[4] }}</td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $h[5] }}</td>
    <td><x-badge-status :status="$h[6]" /></td>
    <td><button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;"><i class="bi bi-eye"></i></button></td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="card-footer d-flex justify-content-between align-items-center">
    <span style="font-size:.875rem;font-weight:700;">Total do período: R$ 1.150,00 recebidos &nbsp;|&nbsp; <span style="color:var(--status-pendente);">R$ 1.150,00 pendentes</span></span>
    <nav><ul class="pagination pagination-sm mb-0">
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
    </ul></nav>
</div>
</div>

@endsection
