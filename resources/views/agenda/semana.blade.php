@extends('layouts.app')
@section('title', 'Agenda — Semana')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Agenda — Semana</li>
</ol></nav>
@endsection
@section('content')

{{-- Controles --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-1">
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i></button>
            <div class="sabenta-card" style="border-radius:10px;padding:0;">
                <div style="padding:.45rem 1.125rem;font-family:var(--font-heading);font-size:.9rem;font-weight:600;">
                    <i class="bi bi-calendar-week me-2" style="color:var(--sabenta-primary);"></i>14 – 20 de Abril de 2025
                </div>
            </div>
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-right"></i></button>
        </div>
        <button class="btn btn-outline-secondary btn-sm">Hoje</button>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('painel.agenda.dia') }}" class="btn btn-outline-secondary">Dia</a>
            <a href="{{ route('painel.agenda.semana') }}" class="btn btn-primary">Semana</a>
            <a href="{{ route('painel.agenda.lista') }}" class="btn btn-outline-secondary">Lista</a>
        </div>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovaSessaoSemana">
        <i class="bi bi-plus-lg"></i> Novo atendimento
    </button>
</div>

{{-- Grade semanal --}}
@php
$dias = [
    ['abrev'=>'Seg','num'=>14,'hoje'=>false],
    ['abrev'=>'Ter','num'=>15,'hoje'=>false],
    ['abrev'=>'Qua','num'=>16,'hoje'=>false],
    ['abrev'=>'Qui','num'=>17,'hoje'=>true],
    ['abrev'=>'Sex','num'=>18,'hoje'=>false],
    ['abrev'=>'Sáb','num'=>19,'hoje'=>false],
    ['abrev'=>'Dom','num'=>20,'hoje'=>false],
];
$sessoesSemana = [
    // [diaIndex, hora, minuto, duracao_slots, cliente, servico, cor, badge]
    [0, 9, 0, 2, 'Mariana C.', 'Atendimento', 'var(--sabenta-primary)', 'badge-confirmado'],
    [1, 10, 0, 2, 'Pedro A.', 'Avaliação', '#8B5CF6', 'badge-pendente'],
    [2, 14, 0, 2, 'Fernanda L.', 'Atendimento', 'var(--status-confirmado)', 'badge-confirmado'],
    [3, 9, 0, 2, 'Mariana C.', 'Atendimento', 'var(--sabenta-primary)', 'badge-confirmado'],
    [3, 14, 30, 2, 'Lucas M.', 'Atendimento', 'var(--sabenta-primary)', 'badge-confirmado'],
    [4, 11, 0, 3, 'Juliana F.', 'Atendimento em Dupla', 'var(--status-pendente)', 'badge-pendente'],
];
@endphp

<div class="sabenta-card" style="overflow:hidden;">
<div style="overflow-x:auto;">
<div style="display:flex;min-width:700px;overflow-y:auto;max-height:calc(100vh - 280px);">
    {{-- Coluna horas --}}
    <div style="width:56px;flex-shrink:0;border-right:1px solid var(--sabenta-border);padding-top:48px;">
        @for($h=8;$h<=18;$h++)
        <div style="height:60px;display:flex;align-items:flex-start;justify-content:flex-end;padding:.35rem .5rem 0 0;border-bottom:1px solid var(--sabenta-border);">
            <span style="font-size:.68rem;font-family:var(--font-heading);color:var(--sabenta-text-muted);font-weight:600;">{{ str_pad($h,2,'0',STR_PAD_LEFT) }}:00</span>
        </div>
        @endfor
    </div>

    {{-- Colunas dos dias --}}
    @foreach($dias as $di => $dia)
    <div style="flex:1;min-width:0;border-right:{{ !$loop->last ? '1px solid var(--sabenta-border)' : 'none' }};position:relative;">
        {{-- Header do dia --}}
        <div style="position:sticky;top:0;z-index:5;height:48px;display:flex;flex-direction:column;align-items:center;justify-content:center;background:{{ $dia['hoje'] ? 'rgba(30,91,173,.06)' : 'var(--sabenta-surface)' }};border-bottom:{{ $dia['hoje'] ? '2px solid var(--sabenta-primary)' : '1px solid var(--sabenta-border)' }};padding:.5rem;">
            <div style="font-size:.7rem;font-family:var(--font-heading);font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:{{ $dia['hoje'] ? 'var(--sabenta-primary)' : 'var(--sabenta-text-muted)' }};">{{ $dia['abrev'] }}</div>
            <div style="font-size:1rem;font-family:var(--font-heading);font-weight:{{ $dia['hoje'] ? '800' : '600' }};color:{{ $dia['hoje'] ? 'var(--sabenta-primary)' : 'var(--sabenta-text)' }};">{{ $dia['num'] }}</div>
        </div>
        {{-- Slots --}}
        @for($h=8;$h<=18;$h++)
        <div style="height:60px;border-bottom:1px solid var(--sabenta-border);background:{{ $dia['hoje'] ? 'rgba(30,91,173,.018)' : 'transparent' }};"></div>
        @endfor
        {{-- Sessões --}}
        @foreach($sessoesSemana as $s)
        @if($s[0] === $di)
        @php $top = ($s[1]-8)*60 + ($s[2]/60*60); @endphp
        <div style="position:absolute;top:{{ $top+48 }}px;left:3px;right:3px;height:{{ $s[3]*30 }}px;background:{{ $s[6] }}1a;border-left:3px solid {{ $s[6] }};border-radius:0 7px 7px 0;padding:.3rem .4rem;cursor:pointer;overflow:hidden;" data-bs-toggle="modal" data-bs-target="#modalDetalhe">
            <div style="font-size:.7rem;font-family:var(--font-heading);font-weight:700;color:{{ $s[6] }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s[4] }}</div>
            <div style="font-size:.65rem;color:var(--sabenta-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s[5] }}</div>
        </div>
        @endif
        @endforeach
    </div>
    @endforeach
</div>
</div>
</div>

{{-- Modal novo atendimento (reutilizado) --}}
<div class="modal fade" id="modalNovaSessaoSemana" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2" style="color:var(--sabenta-primary);"></i>Novo atendimento</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row g-3">
    <div class="col-12"><label class="form-label">Cliente</label>
    <select class="form-select"><option>Selecionar...</option><option>Mariana Costa</option><option>Pedro Alves</option></select></div>
    <div class="col-12"><label class="form-label">Serviço</label>
    <select class="form-select"><option>Atendimento Individual</option><option>Avaliação Inicial</option></select></div>
    <div class="col-6"><label class="form-label">Data</label><input type="date" class="form-control" value="2025-04-17"></div>
    <div class="col-6"><label class="form-label">Horário</label><input type="time" class="form-control" value="09:00"></div>
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

{{-- Modal detalhe --}}
<div class="modal fade" id="modalDetalhe" tabindex="-1">
<div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Detalhe do atendimento</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <div class="d-flex gap-3 align-items-center p-3 mb-3" style="background:var(--sabenta-bg);border-radius:12px;">
        <div class="sabenta-avatar avatar-lg">MC</div>
        <div><div style="font-family:var(--font-heading);font-weight:700;font-size:1rem;">Mariana Costa</div>
        <div style="font-size:.8375rem;color:var(--sabenta-text-muted);">Atendimento Individual · 50 min</div>
        <div style="margin-top:.35rem;"><span class="sabenta-badge badge-confirmado">Confirmada</span></div></div>
    </div>
    <div class="row g-2">
        <div class="col-6"><div style="font-size:.75rem;color:var(--sabenta-text-muted);font-family:var(--font-heading);font-weight:600;text-transform:uppercase;">Data</div>
        <div style="font-size:.9rem;font-weight:600;margin-top:.2rem;">Qui, 17/04 às 09:00</div></div>
        <div class="col-6"><div style="font-size:.75rem;color:var(--sabenta-text-muted);font-family:var(--font-heading);font-weight:600;text-transform:uppercase;">Valor</div>
        <div style="font-size:.9rem;font-weight:600;margin-top:.2rem;">R$ 200,00</div></div>
    </div>
</div>
<div class="modal-footer flex-wrap gap-2">
    <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Realizada</button>
    <button class="btn btn-warning btn-sm" style="color:#fff;"><i class="bi bi-x-circle me-1"></i>Faltou</button>
    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>
</div>
</div></div></div>

@endsection
