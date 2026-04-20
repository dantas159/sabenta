@extends('layouts.app')
@section('title', 'Automações')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Automações</li>
</ol></nav>
@endsection
@section('content')

<x-page-header titulo="Automações e mensagens" sub="Configure lembretes automáticos via WhatsApp" />

{{-- Status WhatsApp --}}
<div class="sabenta-card mb-4">
<div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3" style="padding:1.25rem 1.5rem;">
    <div class="d-flex align-items-center gap-3">
        <div style="width:52px;height:52px;border-radius:14px;background:rgba(37,211,102,.12);display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#25D366;flex-shrink:0;">
            <i class="bi bi-whatsapp"></i>
        </div>
        <div>
            <div style="font-family:var(--font-heading);font-size:1rem;font-weight:700;color:var(--sabenta-text);">WhatsApp Business</div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span style="width:8px;height:8px;border-radius:50%;background:var(--status-confirmado);display:inline-block;"></span>
                <span style="font-size:.8375rem;color:var(--status-confirmado);font-weight:600;">Conectado</span>
                <span style="font-size:.8375rem;color:var(--sabenta-text-muted);">· (11) 99999-8888</span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-qr-code me-1"></i>Reconectar
        </button>
        <button class="btn btn-outline-danger btn-sm">
            <i class="bi bi-power me-1"></i>Desconectar
        </button>
    </div>
</div>
</div>

{{-- Cards de mensagens --}}
@php
$mensagens = [
    ['bi-calendar-check','Confirmação de agendamento','Enviada logo após o paciente agendar uma sessão','1','pendente'],
    ['bi-clock-history','Lembrete 48h antes','Enviado 2 dias antes da sessão para confirmar presença','1','pendente'],
    ['bi-alarm','Lembrete no dia','Enviado na manhã do dia da sessão','1','pendente'],
    ['bi-x-circle','Cancelamento','Enviada quando uma sessão é cancelada pelo profissional','0','cancelado'],
    ['bi-cash-coin','Cobrança de pagamento','Enviada 3 dias após a sessão sem pagamento registrado','1','pendente'],
    ['bi-star','Solicitação de avaliação','Enviada 24h após sessão realizada','0','cancelado'],
];
@endphp

<div class="row g-3 mb-4">
@foreach($mensagens as $i => $m)
<div class="col-md-6 col-xl-4">
<div class="sabenta-card h-100" x-data="{ ativo: {{ $m[3] }} }">
<div class="card-body" style="padding:1.25rem;">
    <div class="d-flex align-items-start gap-3 mb-3">
        <div style="width:42px;height:42px;border-radius:10px;background:rgba(30,91,173,.1);display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:var(--sabenta-primary);flex-shrink:0;">
            <i class="bi {{ $m[0] }}"></i>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-family:var(--font-heading);font-weight:700;font-size:.9rem;line-height:1.3;">{{ $m[1] }}</div>
            <div style="font-size:.78rem;color:var(--sabenta-text-muted);margin-top:.25rem;line-height:1.4;">{{ $m[2] }}</div>
        </div>
        <div class="form-check form-switch ms-2 mt-1" style="flex-shrink:0;">
            <input class="form-check-input" type="checkbox" :checked="ativo" @change="ativo = !ativo" style="width:2.2em;height:1.2em;cursor:pointer;">
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between">
        <span class="sabenta-badge" :class="ativo ? 'badge-confirmado' : 'badge-cancelado'" x-text="ativo ? 'Ativa' : 'Desativada'"></span>
        <button class="btn btn-sm btn-outline-secondary" style="font-size:.8rem;padding:.3rem .7rem;" data-bs-toggle="modal" data-bs-target="#modalEditarMsg">
            <i class="bi bi-pencil me-1"></i>Editar
        </button>
    </div>
</div>
</div>
</div>
@endforeach
</div>

{{-- Log de envios --}}
<div class="sabenta-card">
<div class="card-header">
    <span class="card-header-title"><i class="bi bi-clock-history me-2" style="color:var(--sabenta-primary);"></i>Log de envios recentes</span>
    <span class="sabenta-badge badge-confirmado" style="font-size:.7rem;">47 enviados este mês</span>
</div>
<div style="overflow-x:auto;">
@php
$logs = [
    ['17/04 08:15','Mariana Costa','(11) 99211-4455','Lembrete no dia','entregue'],
    ['16/04 12:00','Lucas Mendes','(11) 97654-3321','Lembrete 48h antes','entregue'],
    ['16/04 09:30','Fernanda Lima','(11) 91234-5678','Lembrete no dia','entregue'],
    ['15/04 18:00','Pedro Alves','(21) 98765-3210','Confirmação de agendamento','entregue'],
    ['14/04 08:15','Mariana Costa','(11) 99211-4455','Lembrete no dia','entregue'],
    ['13/04 12:00','Mariana Costa','(11) 99211-4455','Lembrete 48h antes','entregue'],
    ['10/04 14:22','Juliana Ferreira','(31) 99988-7766','Cobrança de pagamento','falhou'],
    ['09/04 09:00','Lucas Mendes','(11) 97654-3321','Lembrete no dia','entregue'],
    ['08/04 18:00','Fernanda Lima','(11) 91234-5678','Confirmação de agendamento','entregue'],
    ['07/04 08:15','Pedro Alves','(21) 98765-3210','Lembrete no dia','entregue'],
];
@endphp
<table class="sabenta-table">
<thead><tr><th>Data/hora</th><th>Paciente</th><th>Número</th><th>Mensagem</th><th>Status</th></tr></thead>
<tbody>
@foreach($logs as $l)
<tr>
    <td style="font-size:.8375rem;font-family:var(--font-heading);font-weight:600;">{{ $l[0] }}</td>
    <td style="font-size:.875rem;font-weight:500;">{{ $l[1] }}</td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $l[2] }}</td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $l[3] }}</td>
    <td>
        @if($l[4] === 'entregue')
        <span class="sabenta-badge badge-confirmado" style="font-size:.7rem;"><i class="bi bi-check-all me-1"></i>Entregue</span>
        @else
        <span class="sabenta-badge badge-faltou" style="font-size:.7rem;"><i class="bi bi-x-circle me-1"></i>Falhou</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="card-footer">
    <nav><ul class="pagination pagination-sm mb-0">
        <li class="page-item active"><a class="page-link" href="#">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
    </ul></nav>
</div>
</div>

{{-- Modal 6-A: Editar mensagem --}}
<div class="modal fade" id="modalEditarMsg" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-pencil me-2" style="color:var(--sabenta-primary);"></i>Editar mensagem — Lembrete 48h antes</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body" x-data="{ msg: 'Olá {nome}! Lembrando que você tem uma sessão amanhã, {data} às {hora}. Para confirmar, responda SIM. Para cancelar ou reagendar, entre em contato. — Dra. Ana Souza 😊' }">
    <div class="row g-3">
        <div class="col-md-7">
            <label class="form-label">Texto da mensagem</label>
            <textarea class="form-control" rows="6" x-model="msg" style="font-size:.9rem;line-height:1.7;"></textarea>
            <div class="form-text mt-1">
                <strong>Variáveis disponíveis:</strong>
                <span class="sabenta-badge badge-realizado" style="font-size:.7rem;cursor:pointer;" @click="msg += ' {nome}'">{nome}</span>
                <span class="sabenta-badge badge-realizado" style="font-size:.7rem;cursor:pointer;" @click="msg += ' {data}'">{data}</span>
                <span class="sabenta-badge badge-realizado" style="font-size:.7rem;cursor:pointer;" @click="msg += ' {hora}'">{hora}</span>
                <span class="sabenta-badge badge-realizado" style="font-size:.7rem;cursor:pointer;" @click="msg += ' {servico}'">{servico}</span>
            </div>
        </div>
        <div class="col-md-5">
            <label class="form-label">Preview ao vivo</label>
            <div style="background:#e5ddd5;border-radius:12px;padding:1rem;min-height:160px;">
                <div style="background:#fff;border-radius:8px 8px 8px 0;padding:.75rem;font-size:.85rem;line-height:1.6;box-shadow:0 1px 2px rgba(0,0,0,.1);"
                     x-text="msg.replace('{nome}', 'Mariana').replace('{data}', '16/04/2025').replace('{hora}', '09:00').replace('{servico}', 'Terapia Individual')"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar mensagem</button>
</div>
</div></div></div>

@endsection
