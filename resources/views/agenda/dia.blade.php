@extends('layouts.app')
@section('title', 'Agenda — Dia')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Agenda — Dia</li>
</ol></nav>
@endsection
@section('content')

{{-- Controles --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-1">
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i></button>
            <div class="sabenta-card" style="border-radius:10px;padding:0;">
                <div style="padding:.45rem 1.125rem;font-family:var(--font-heading);font-size:.9rem;font-weight:600;color:var(--sabenta-text);">
                    <i class="bi bi-calendar3 me-2" style="color:var(--sabenta-primary);"></i>Qui, 17 de Abril de 2025
                </div>
            </div>
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-right"></i></button>
        </div>
        <button class="btn btn-outline-secondary btn-sm">Hoje</button>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('painel.agenda.dia') }}" class="btn btn-primary">Dia</a>
            <a href="{{ route('painel.agenda.semana') }}" class="btn btn-outline-secondary">Semana</a>
            <a href="{{ route('painel.agenda.lista') }}" class="btn btn-outline-secondary">Lista</a>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBloquear">
            <i class="bi bi-lock"></i> Bloquear
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovaSessao">
            <i class="bi bi-plus-lg"></i> Nova sessão
        </button>
    </div>
</div>

{{-- Grade --}}
<div class="sabenta-card" style="overflow:hidden;">
<div style="display:flex;overflow-y:auto;max-height:calc(100vh - 270px);">
    {{-- Coluna de horas --}}
    <div style="width:64px;flex-shrink:0;border-right:1px solid var(--sabenta-border);">
        @php
        $slots = [];
        for($h=8;$h<=19;$h++) {
            $slots[] = ['h'=>$h,'m'=>0,'label'=>str_pad($h,2,'0',STR_PAD_LEFT).':00'];
            if($h<19) $slots[] = ['h'=>$h,'m'=>30,'label'=>''];
        }
        @endphp
        @foreach($slots as $slot)
        <div style="height:60px;display:flex;align-items:flex-start;justify-content:flex-end;padding:.4rem .5rem 0 0;border-bottom:{{ $slot['m']==0 ? '1px solid var(--sabenta-border)' : '1px dashed rgba(221,228,240,.5)' }};">
            @if($slot['m']==0)
            <span style="font-size:.7rem;font-family:var(--font-heading);color:var(--sabenta-text-muted);font-weight:600;">{{ $slot['label'] }}</span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Coluna de sessões --}}
    <div style="flex:1;position:relative;">
        @foreach($slots as $i => $slot)
        <div style="height:60px;border-bottom:{{ $slot['m']==0 ? '1px solid var(--sabenta-border)' : '1px dashed rgba(221,228,240,.5)' }};"></div>
        @endforeach

        {{-- Sessão 09:00 --}}
        <div style="position:absolute;top:{{ (1)*120 }}px;left:8px;right:8px;height:100px;background:rgba(30,91,173,.08);border:1.5px solid var(--sabenta-primary);border-radius:10px;padding:.625rem .875rem;cursor:pointer;" onclick="document.getElementById('modalDetalhe').querySelector('.modal').classList; new bootstrap.Modal(document.getElementById('modalDetalhe')).show();" data-bs-toggle="modal" data-bs-target="#modalDetalhe">
            <div style="font-family:var(--font-heading);font-size:.8rem;font-weight:700;color:var(--sabenta-primary);">09:00 – 09:50</div>
            <div style="font-size:.875rem;font-weight:600;color:var(--sabenta-text);margin-top:.2rem;">Mariana Costa</div>
            <div style="font-size:.775rem;color:var(--sabenta-text-muted);"><i class="bi bi-card-text me-1"></i>Terapia Individual</div>
            <span class="sabenta-badge badge-confirmado" style="font-size:.68rem;margin-top:.25rem;">Confirmada</span>
        </div>

        {{-- Sessão 11:00 --}}
        <div style="position:absolute;top:{{ (3)*120 }}px;left:8px;right:8px;height:100px;background:rgba(139,92,246,.08);border:1.5px solid #8B5CF6;border-radius:10px;padding:.625rem .875rem;cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalDetalhe">
            <div style="font-family:var(--font-heading);font-size:.8rem;font-weight:700;color:#8B5CF6;">11:00 – 12:00</div>
            <div style="font-size:.875rem;font-weight:600;color:var(--sabenta-text);margin-top:.2rem;">Pedro Alves</div>
            <div style="font-size:.775rem;color:var(--sabenta-text-muted);"><i class="bi bi-card-text me-1"></i>Avaliação Psicológica</div>
            <span class="sabenta-badge badge-pendente" style="font-size:.68rem;margin-top:.25rem;">Pendente</span>
        </div>

        {{-- Bloqueio 12:00 --}}
        <div style="position:absolute;top:{{ (4)*120 }}px;left:8px;right:8px;height:60px;background:rgba(107,122,153,.08);border:1.5px dashed #b0bbd0;border-radius:10px;padding:.5rem .875rem;display:flex;align-items:center;gap:.625rem;">
            <i class="bi bi-lock" style="color:var(--sabenta-text-muted);"></i>
            <div style="font-size:.8rem;font-weight:600;color:var(--sabenta-text-muted);font-family:var(--font-heading);">Almoço — 12:00 a 13:00</div>
        </div>

        {{-- Sessão 14:30 --}}
        <div style="position:absolute;top:{{ (6)*120+60 }}px;left:8px;right:8px;height:100px;background:rgba(29,158,117,.08);border:1.5px solid var(--status-confirmado);border-radius:10px;padding:.625rem .875rem;cursor:pointer;" data-bs-toggle="modal" data-bs-target="#modalDetalhe">
            <div style="font-family:var(--font-heading);font-size:.8rem;font-weight:700;color:var(--status-confirmado);">14:30 – 15:20</div>
            <div style="font-size:.875rem;font-weight:600;color:var(--sabenta-text);margin-top:.2rem;">Fernanda Lima</div>
            <div style="font-size:.775rem;color:var(--sabenta-text-muted);"><i class="bi bi-card-text me-1"></i>Terapia Individual</div>
            <span class="sabenta-badge badge-confirmado" style="font-size:.68rem;margin-top:.25rem;">Confirmada</span>
        </div>
    </div>
</div>
</div>

{{-- MODAL 3-A: Nova Sessão --}}
<div class="modal fade" id="modalNovaSessao" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2" style="color:var(--sabenta-primary);"></i>Nova sessão</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form method="POST" action="#" x-data="{ recorrente: false }">@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Paciente</label>
        <select class="form-select">
            <option value="">Selecionar paciente...</option>
            <option>Mariana Costa</option><option>Pedro Alves</option>
            <option>Fernanda Lima</option><option>Lucas Mendes</option>
            <option>Juliana Ferreira</option><option>Rafael Santos</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Serviço</label>
        <select class="form-select">
            <option value="">Selecionar serviço...</option>
            <option>Terapia Individual</option><option>Terapia de Casal</option>
            <option>Avaliação Psicológica</option><option>Psicoterapia Infantil</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Data</label>
        <input type="date" class="form-control" value="2025-04-17">
    </div>
    <div class="col-md-4">
        <label class="form-label">Horário</label>
        <input type="time" class="form-control" value="09:00">
    </div>
    <div class="col-md-4">
        <label class="form-label">Duração</label>
        <select class="form-select">
            <option>30 min</option><option>45 min</option>
            <option selected>50 min</option><option>60 min</option><option>90 min</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Valor</label>
        <div class="input-group"><span class="input-group-text">R$</span>
        <input type="number" class="form-control" placeholder="200,00"></div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Recorrência</label>
        <select class="form-select" @change="recorrente = $event.target.value !== ''">
            <option value="">Sem recorrência</option>
            <option value="semanal">Semanal</option>
            <option value="quinzenal">Quinzenal</option>
            <option value="mensal">Mensal</option>
        </select>
    </div>
    <div class="col-md-6" x-show="recorrente" style="display:none;">
        <label class="form-label">Repetir até</label>
        <input type="date" class="form-control">
    </div>
    <div class="col-12">
        <label class="form-label">Observações</label>
        <textarea class="form-control" rows="2" placeholder="Anotações internas sobre esta sessão..."></textarea>
    </div>
</div>
</form>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar sessão</button>
</div>
</div></div></div>

{{-- MODAL 3-B: Detalhe --}}
<div class="modal fade" id="modalDetalhe" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title">Detalhe da sessão</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="d-flex align-items-center gap-3 p-3 mb-3" style="background:var(--sabenta-bg);border-radius:12px;">
        <div class="sabenta-avatar avatar-lg">MC</div>
        <div>
            <div style="font-family:var(--font-heading);font-weight:700;font-size:1rem;">Mariana Costa</div>
            <div style="font-size:.8375rem;color:var(--sabenta-text-muted);">Terapia Individual · 50 min</div>
            <div style="margin-top:.35rem;"><span class="sabenta-badge badge-confirmado">Confirmada</span></div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-6">
            <div style="font-size:.75rem;color:var(--sabenta-text-muted);font-family:var(--font-heading);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Data e Hora</div>
            <div style="font-size:.9rem;font-weight:600;margin-top:.2rem;">Qui, 17/04 às 09:00</div>
        </div>
        <div class="col-6">
            <div style="font-size:.75rem;color:var(--sabenta-text-muted);font-family:var(--font-heading);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Valor</div>
            <div style="font-size:.9rem;font-weight:600;margin-top:.2rem;">R$ 200,00</div>
        </div>
        <div class="col-12">
            <div style="font-size:.75rem;color:var(--sabenta-text-muted);font-family:var(--font-heading);font-weight:600;text-transform:uppercase;letter-spacing:.05em;">Pagamento</div>
            <div style="margin-top:.35rem;"><span class="sabenta-badge badge-pendente">Pendente</span></div>
        </div>
    </div>
</div>
<div class="modal-footer flex-wrap gap-2">
    <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Realizada</button>
    <button class="btn btn-warning btn-sm" style="color:#fff;"><i class="bi bi-x-circle me-1"></i>Faltou</button>
    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil me-1"></i>Editar</button>
    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalExcluirSerie">
        <i class="bi bi-trash me-1"></i>Excluir
    </button>
</div>
</div></div></div>

{{-- MODAL 3-C: Bloquear --}}
<div class="modal fade" id="modalBloquear" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-lock me-2"></i>Bloquear horário</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row g-3">
    <div class="col-12"><label class="form-label">Motivo</label>
    <input type="text" class="form-control" placeholder="Ex: Supervisão, Almoço, Reunião..."></div>
    <div class="col-6"><label class="form-label">Data início</label>
    <input type="date" class="form-control" value="2025-04-17"></div>
    <div class="col-6"><label class="form-label">Data fim</label>
    <input type="date" class="form-control" value="2025-04-17"></div>
    <div class="col-6"><label class="form-label">Hora início</label>
    <input type="time" class="form-control" value="12:00"></div>
    <div class="col-6"><label class="form-label">Hora fim</label>
    <input type="time" class="form-control" value="13:00"></div>
</div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary"><i class="bi bi-lock me-1"></i>Salvar bloqueio</button>
</div>
</div></div></div>

{{-- MODAL 3-D: Excluir série --}}
<div class="modal fade" id="modalExcluirSerie" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title text-danger"><i class="bi bi-trash me-2"></i>Excluir sessão</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <p style="font-size:.9rem;color:var(--sabenta-text-muted);">Esta sessão faz parte de uma série recorrente. O que deseja excluir?</p>
    <div class="d-flex flex-column gap-2 mt-3">
        @foreach([['esta','Apenas esta sessão','Qui, 17/04 às 09:00'],['futuras','Esta e as futuras','A partir de hoje'],['todas','Todas as sessões da série','Inclui passadas e futuras']] as $opt)
        <label class="d-flex align-items-start gap-3 p-3 rounded-3" style="border:1.5px solid var(--sabenta-border);cursor:pointer;">
            <input type="radio" name="excluir" value="{{ $opt[0] }}" class="form-check-input mt-1" {{ $loop->first ? 'checked' : '' }}>
            <div><div style="font-weight:600;font-size:.875rem;">{{ $opt[1] }}</div>
            <div style="font-size:.8rem;color:var(--sabenta-text-muted);">{{ $opt[2] }}</div></div>
        </label>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-danger"><i class="bi bi-trash me-1"></i>Confirmar exclusão</button>
</div>
</div></div></div>

@endsection
