@extends('layouts.app')
@section('title', 'Configurações')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Configurações</li>
</ol></nav>
@endsection
@section('content')

<x-page-header titulo="Configurações" sub="Personalize sua agenda e conta" />

{{-- Nav tabs --}}
<div class="sabenta-card">
<div style="border-bottom:1px solid var(--sabenta-border);padding:0 1.375rem;">
<ul class="nav nav-tabs border-0" id="configTabs" role="tablist" style="flex-wrap:nowrap;overflow-x:auto;scrollbar-width:none;">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabHorarios" style="white-space:nowrap;">
        <i class="bi bi-clock me-2"></i>Horários
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabServicos" style="white-space:nowrap;">
        <i class="bi bi-card-list me-2"></i>Serviços
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabCancelamentos" style="white-space:nowrap;">
        <i class="bi bi-calendar-x me-2"></i>Cancelamentos
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabConta" style="white-space:nowrap;">
        <i class="bi bi-person-circle me-2"></i>Minha conta
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabAssinatura" style="white-space:nowrap;">
        <i class="bi bi-credit-card me-2"></i>Assinatura
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPermissoes" style="white-space:nowrap;">
        <i class="bi bi-shield-check me-2"></i>Permissões
    </button></li>
</ul>
</div>
<div class="tab-content">

    {{-- Horários --}}
    <div class="tab-pane fade show active" id="tabHorarios">
    <div class="card-body">
    <p style="font-size:.875rem;color:var(--sabenta-text-muted);margin-bottom:1.25rem;">Configure os dias e horários em que você atende pacientes.</p>
    @php
    $dias = [
        ['Segunda-feira','08:00','18:00','60',true],
        ['Terça-feira','08:00','18:00','60',true],
        ['Quarta-feira','08:00','18:00','60',true],
        ['Quinta-feira','08:00','18:00','60',true],
        ['Sexta-feira','08:00','17:00','60',true],
        ['Sábado','09:00','12:00','60',false],
        ['Domingo','—','—','—',false],
    ];
    @endphp
    <div style="overflow-x:auto;">
    <table class="sabenta-table">
    <thead><tr><th>Dia</th><th>Ativo</th><th>Hora início</th><th>Hora fim</th><th>Intervalo (min)</th></tr></thead>
    <tbody>
    @foreach($dias as $d)
    <tr>
        <td style="font-weight:600;font-size:.875rem;">{{ $d[0] }}</td>
        <td>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" {{ $d[4] ? 'checked' : '' }}>
            </div>
        </td>
        <td><input type="time" class="form-control form-control-sm" style="width:110px;" value="{{ $d[1] !== '—' ? $d[1] : '' }}" {{ !$d[4] ? 'disabled' : '' }}></td>
        <td><input type="time" class="form-control form-control-sm" style="width:110px;" value="{{ $d[2] !== '—' ? $d[2] : '' }}" {{ !$d[4] ? 'disabled' : '' }}></td>
        <td>
            <select class="form-select form-select-sm" style="width:100px;" {{ !$d[4] ? 'disabled' : '' }}>
                <option {{ $d[3] == '30' ? 'selected' : '' }}>30</option>
                <option {{ $d[3] == '60' ? 'selected' : '' }}>60</option>
            </select>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>
    <div class="mt-4"><button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar horários</button></div>
    </div>
    </div>

    {{-- Serviços --}}
    <div class="tab-pane fade" id="tabServicos">
    <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p style="font-size:.875rem;color:var(--sabenta-text-muted);margin:0;">Serviços disponíveis para agendamento.</p>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoServico">
            <i class="bi bi-plus-lg me-1"></i>Novo serviço
        </button>
    </div>
    @php
    $servicos = [
        ['Consulta Individual','50 min','R$ 200,00','Sim',true],
        ['Consulta em Dupla','90 min','R$ 400,00','Sim',true],
        ['Avaliação Inicial','60 min','R$ 350,00','Não',true],
        ['Consulta Online','45 min','R$ 180,00','Sim',false],
    ];
    @endphp
    <table class="sabenta-table">
    <thead><tr><th>Serviço</th><th>Duração</th><th>Valor</th><th>Agend. online</th><th>Ativo</th><th></th></tr></thead>
    <tbody>
    @foreach($servicos as $s)
    <tr>
        <td style="font-weight:600;font-size:.875rem;">{{ $s[0] }}</td>
        <td style="font-size:.8375rem;">{{ $s[1] }}</td>
        <td style="font-weight:700;font-size:.875rem;">{{ $s[2] }}</td>
        <td><span class="sabenta-badge {{ $s[3]==='Sim' ? 'badge-confirmado' : 'badge-cancelado' }}" style="font-size:.7rem;">{{ $s[3] }}</span></td>
        <td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" {{ $s[4] ? 'checked' : '' }}></div></td>
        <td>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;" data-bs-toggle="modal" data-bs-target="#modalNovoServico"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-outline-danger" style="padding:.25rem .55rem;"><i class="bi bi-trash"></i></button>
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>
    </div>

    {{-- Cancelamentos --}}
    <div class="tab-pane fade" id="tabCancelamentos">
    <div class="card-body" style="max-width:600px;">
    <div class="mb-4">
        <label class="form-label">Janela mínima de cancelamento</label>
        <select class="form-select" style="max-width:280px;">
            <option>Sem restrição</option>
            <option>12 horas antes</option>
            <option selected>24 horas antes</option>
            <option>48 horas antes</option>
            <option>72 horas antes</option>
        </select>
        <div class="form-text">Pacientes não poderão cancelar com menos antecedência que o definido.</div>
    </div>
    <div class="mb-4">
        <label class="form-label">Política de cancelamento</label>
        <textarea class="form-control" rows="6" style="font-size:.9rem;line-height:1.7;">Cancelamentos devem ser realizados com no mínimo 24 horas de antecedência. Cancelamentos com menos de 24 horas ou ausências sem aviso podem estar sujeitos à cobrança de 50% do valor da sessão, a critério do profissional.

Para reagendamentos, entre em contato por WhatsApp.</textarea>
        <div class="form-text">Esta política será exibida na sua página pública de agendamento.</div>
    </div>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar política</button>
    </div>
    </div>

    {{-- Minha conta --}}
    <div class="tab-pane fade" id="tabConta">
    <div class="card-body">
    <div class="row g-4">
        <div class="col-md-4 text-center">
            <div class="sabenta-avatar avatar-lg mx-auto mb-3" style="width:80px;height:80px;font-size:1.5rem;border-radius:20px;">AS</div>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-camera me-1"></i>Alterar foto
            </button>
            <div class="form-text mt-1">JPG, PNG ou GIF · Máx. 2MB</div>
        </div>
        <div class="col-md-8">
            <div class="row g-3">
                <div class="col-12"><label class="form-label">Nome completo</label>
                <input type="text" class="form-control" value="Dra. Ana Souza"></div>
                <div class="col-md-6"><label class="form-label">E-mail</label>
                <input type="email" class="form-control" value="ana.souza@psicologia.com.br"></div>
                <div class="col-md-6"><label class="form-label">WhatsApp</label>
                <input type="tel" class="form-control" value="(11) 99999-8888"></div>
                <div class="col-md-6"><label class="form-label">CRP</label>
                <input type="text" class="form-control" placeholder="CRP 06/123456"></div>
                <div class="col-md-6"><label class="form-label">Especialidade</label>
                <input type="text" class="form-control" value="Psicóloga Clínica"></div>
                <div class="col-12"><label class="form-label">Bio profissional</label>
                <textarea class="form-control" rows="3" style="font-size:.9rem;">Psicóloga clínica com 8 anos de experiência, especializada em terapia cognitivo-comportamental e psicodinâmica. Atendo adultos e casais.</textarea></div>
                <div class="col-12"><button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar dados</button></div>
            </div>
        </div>
    </div>
    <hr style="border-color:var(--sabenta-border);margin:2rem 0;">
    <h6 style="font-family:var(--font-heading);font-weight:700;margin-bottom:1.25rem;">Alterar senha</h6>
    <div class="row g-3" style="max-width:480px;" x-data="{ show:false }">
        <div class="col-12"><label class="form-label">Senha atual</label>
        <div class="input-group"><input :type="show?'text':'password'" class="form-control" placeholder="••••••••">
        <button type="button" class="input-group-text" style="cursor:pointer;background:transparent;" @click="show=!show"><i class="bi" :class="show?'bi-eye-slash':'bi-eye'"></i></button></div></div>
        <div class="col-6"><label class="form-label">Nova senha</label><input type="password" class="form-control" placeholder="Min. 8 chars"></div>
        <div class="col-6"><label class="form-label">Confirmar</label><input type="password" class="form-control" placeholder="Repita"></div>
        <div class="col-12"><button class="btn btn-outline-secondary"><i class="bi bi-lock me-1"></i>Alterar senha</button></div>
    </div>
    </div>
    </div>

    {{-- Assinatura --}}
    <div class="tab-pane fade" id="tabAssinatura">
    <div class="card-body">
    {{-- Plano atual --}}
    <div class="p-4 mb-4 rounded-3" style="background:linear-gradient(135deg,rgba(30,91,173,.08) 0%,rgba(99,179,245,.1) 100%);border:2px solid rgba(30,91,173,.2);">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <div style="font-size:.7rem;font-family:var(--font-heading);font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--sabenta-primary);margin-bottom:.35rem;">Plano atual</div>
                <h3 style="font-family:var(--font-heading);font-size:1.375rem;font-weight:800;color:var(--sabenta-text);margin-bottom:.25rem;">Solo</h3>
                <div style="font-size:.875rem;color:var(--sabenta-text-muted);">R$ 79,00/mês · Renovação em 15/05/2025</div>
                <div class="mt-2">
                    <span class="sabenta-badge badge-confirmado">Ativo</span>
                </div>
            </div>
            <a href="{{ route('planos') }}" class="btn btn-primary">
                <i class="bi bi-arrow-up-circle me-1"></i>Fazer upgrade
            </a>
        </div>
    </div>
    {{-- Recursos --}}
    <div class="row g-2 mb-4">
        @foreach(['1 profissional','Agenda ilimitada','Agendamento online','Automações WhatsApp','Página profissional','Relatórios básicos'] as $r)
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-2" style="font-size:.875rem;">
                <i class="bi bi-check-circle-fill" style="color:var(--status-confirmado);"></i>
                {{ $r }}
            </div>
        </div>
        @endforeach
    </div>
    {{-- Faturas --}}
    <h6 style="font-family:var(--font-heading);font-weight:700;margin-bottom:1rem;">Histórico de faturas</h6>
    <table class="sabenta-table">
    <thead><tr><th>Data</th><th>Plano</th><th>Valor</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @foreach([['15/04/2025','Solo mensal','R$ 79,00','pago'],['15/03/2025','Solo mensal','R$ 79,00','pago'],['15/02/2025','Solo mensal','R$ 79,00','pago'],['15/01/2025','Solo mensal','R$ 79,00','pago']] as $f)
    <tr>
        <td style="font-size:.875rem;font-weight:600;">{{ $f[0] }}</td>
        <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $f[1] }}</td>
        <td style="font-weight:700;">{{ $f[2] }}</td>
        <td><x-badge-status :status="$f[3]" /></td>
        <td><button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;"><i class="bi bi-download"></i></button></td>
    </tr>
    @endforeach
    </tbody>
    </table>
    </div>
    </div>

</div>
</div>

    {{-- Permissões --}}
    <div class="tab-pane fade" id="tabPermissoes">
    <div class="card-body">
        <p style="font-size:.875rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;">
            Veja o que cada perfil pode acessar na plataforma. Para alterar o perfil de um membro, acesse
            <a href="{{ route('painel.equipe.index') }}">Equipe</a>.
        </p>

        @php
        $matriz = [
            ['Painel (visão geral)',              true,  true,  true],
            ['Agenda — visualizar todas',         true,  false, true],
            ['Agenda — visualizar própria',       true,  true,  true],
            ['Agenda — criar/editar sessões',     true,  true,  true],
            ['Pacientes — cadastrar/editar',      true,  true,  true],
            ['Clientes — observações',             true,  true,  false],
            ['Financeiro — visualizar tudo',      true,  false, false],
            ['Financeiro — visualizar próprio',   true,  true,  false],
            ['Financeiro — registrar pagamentos', true,  true,  true],
            ['Automações (WhatsApp)',              true,  true,  false],
            ['Minha Página (perfil público)',      true,  true,  false],
            ['Equipe — visualizar',               true,  false, false],
            ['Equipe — convidar/remover',         true,  false, false],
            ['Configurações — horários/serviços', true,  true,  false],
            ['Configurações — conta/assinatura',  true,  false, false],
        ];
        @endphp

        <div style="overflow-x:auto;">
        <table class="sabenta-table">
        <thead>
            <tr>
                <th style="width:45%;">Funcionalidade</th>
                <th class="text-center">
                    <span class="sabenta-badge badge-confirmado" style="font-size:0.7rem;">Administrador</span>
                </th>
                <th class="text-center">
                    <span class="sabenta-badge badge-realizado" style="font-size:0.7rem;">Profissional</span>
                </th>
                <th class="text-center">
                    <span class="sabenta-badge badge-pendente" style="font-size:0.7rem;">Recepcionista</span>
                </th>
            </tr>
        </thead>
        <tbody>
        @foreach($matriz as $row)
        <tr>
            <td style="font-size:.875rem;">{{ $row[0] }}</td>
            @foreach([$row[1], $row[2], $row[3]] as $val)
            <td class="text-center">
                @if($val === true)
                    <i class="bi bi-check-circle-fill" style="color:var(--sabenta-success);font-size:1rem;"></i>
                @else
                    <i class="bi bi-x-circle" style="color:var(--sabenta-border);font-size:1rem;"></i>
                @endif
            </td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
        </table>
        </div>

        <div class="mt-4 p-3 rounded-3" style="background:var(--sabenta-primary-light);font-size:0.8125rem;color:var(--sabenta-primary);">
            <i class="bi bi-info-circle me-1"></i>
            As permissões acima se aplicam aos planos <strong>Clínica Pequena</strong> e <strong>Clínica Média</strong>.
            No plano Solo, existe apenas um único usuário com acesso total.
            <a href="{{ route('planos') }}" class="ms-1">Ver planos</a>.
        </div>
    </div>
    </div>

{{-- Modal novo serviço --}}
<div class="modal fade" id="modalNovoServico" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-card-list me-2" style="color:var(--sabenta-primary);"></i>Novo serviço</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="row g-3">
    <div class="col-12"><label class="form-label">Nome do serviço</label>
    <input type="text" class="form-control" placeholder="Ex: Consulta Individual"></div>
    <div class="col-md-6"><label class="form-label">Duração padrão</label>
    <select class="form-select"><option>30 min</option><option>45 min</option><option selected>50 min</option><option>60 min</option><option>90 min</option></select></div>
    <div class="col-md-6"><label class="form-label">Valor padrão</label>
    <div class="input-group"><span class="input-group-text">R$</span><input type="number" class="form-control" placeholder="200.00"></div></div>
    <div class="col-12"><label class="form-label">Descrição (exibida na página pública)</label>
    <textarea class="form-control" rows="3" placeholder="Descreva brevemente este serviço..."></textarea></div>
    <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="agOnline" checked>
    <label class="form-check-label" for="agOnline" style="font-size:.875rem;">Disponível para agendamento online</label></div></div>
</div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar serviço</button>
</div>
</div></div></div>

@endsection
