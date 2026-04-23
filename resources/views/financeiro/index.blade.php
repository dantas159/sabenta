@extends('layouts.app')
@section('title', 'Financeiro')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Financeiro</li>
</ol></nav>
@endsection
@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h1 class="page-header__title">Financeiro</h1>
        <div class="page-header__sub">Resumo do período selecionado</div>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <select class="form-select" style="min-width:200px;font-size:.875rem;">
            <option>Este mês (Abril/2025)</option>
            <option>Mês anterior (Março/2025)</option>
            <option>Últimos 3 meses</option>
            <option>Personalizado</option>
        </select>
        <a href="{{ route('painel.financeiro.historico') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-clock-history me-1"></i>Histórico
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <x-stat-card titulo="Receita realizada" valor="R$ 3.400" icone="cash-coin" cor="success" delta="+8% vs mês anterior" :deltaUp="true"/>
    </div>
    <div class="col-6 col-xl-3">
        <x-stat-card titulo="A receber" valor="R$ 800" icone="clock" cor="warning" delta="4 atendimentos pendentes" :deltaUp="false"/>
    </div>
    <div class="col-6 col-xl-3">
        <x-stat-card titulo="Atendimentos realizados" valor="17" icone="calendar-check" cor="primary"/>
    </div>
    <div class="col-6 col-xl-3">
        <x-stat-card titulo="Ticket médio" valor="R$ 200" icone="graph-up" cor="purple" delta="+R$ 12 vs mês anterior" :deltaUp="true"/>
    </div>
</div>

{{-- Gráfico --}}
<div class="sabenta-card mb-3">
<div class="card-header">
    <span class="card-header-title"><i class="bi bi-bar-chart me-2" style="color:var(--sabenta-primary);"></i>Receita por semana — Abril 2025</span>
</div>
<div class="card-body">
    <canvas id="graficoReceita" height="100"></canvas>
</div>
</div>

{{-- Tabela de atendimentos --}}
<div class="sabenta-card">
<div class="card-header">
    <span class="card-header-title">Atendimentos do período</span>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-download me-1"></i>Exportar</button>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalPagamento">
            <i class="bi bi-cash me-1"></i>Registrar pagamento
        </button>
    </div>
</div>
<div style="overflow-x:auto;">
@php
$sessoes = [
    ['07/04','Mariana Costa','Atendimento Individual','R$ 200','Pix','pago'],
    ['07/04','Pedro Alves','Avaliação Inicial','R$ 350','Transferência','pago'],
    ['08/04','Fernanda Lima','Atendimento Individual','R$ 200','—','pendente'],
    ['09/04','Lucas Mendes','Atendimento Individual','R$ 200','Pix','pago'],
    ['10/04','Juliana Ferreira','Atendimento em Dupla','R$ 400','—','pendente'],
    ['14/04','Mariana Costa','Atendimento Individual','R$ 200','Pix','pago'],
    ['15/04','Pedro Alves','Avaliação Inicial','R$ 350','—','pendente'],
    ['16/04','Lucas Mendes','Atendimento Individual','R$ 200','Pix','pago'],
];
@endphp
<table class="sabenta-table">
<thead><tr><th>Data</th><th>Cliente</th><th>Serviço</th><th>Valor</th><th>Pagamento</th><th>Status</th><th></th></tr></thead>
<tbody>
@foreach($sessoes as $s)
<tr>
    <td style="font-weight:600;font-size:.875rem;">{{ $s[0] }}</td>
    <td><span style="font-weight:500;font-size:.875rem;">{{ $s[1] }}</span></td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $s[2] }}</td>
    <td style="font-weight:700;font-size:.875rem;">{{ $s[3] }}</td>
    <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $s[4] }}</td>
    <td><x-badge-status :status="$s[5]" /></td>
    <td>
        <div class="d-flex gap-1">
            @if($s[5] === 'pendente')
            <button class="btn btn-sm btn-success" style="padding:.25rem .55rem;font-size:.8rem;" data-bs-toggle="modal" data-bs-target="#modalPagamento">
                <i class="bi bi-cash me-1"></i>Pago
            </button>
            <button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;font-size:.8rem;" data-bs-toggle="modal" data-bs-target="#modalCobranca">
                <i class="bi bi-send me-1"></i>Cobrar
            </button>
            @else
            <button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;" title="Ver comprovante"><i class="bi bi-receipt"></i></button>
            @endif
        </div>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="card-footer d-flex justify-content-between align-items-center">
    <span style="font-size:.875rem;font-weight:600;">Total do período: <span style="color:var(--status-confirmado);">R$ 3.400 recebidos</span> + <span style="color:var(--status-pendente);">R$ 800 pendentes</span></span>
    <span style="font-size:.8rem;color:var(--sabenta-text-muted);">8 de 17 atendimentos exibidos</span>
</div>
</div>

{{-- Modal 5-A: Registrar pagamento --}}
<div class="modal fade" id="modalPagamento" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-cash-coin me-2" style="color:var(--status-confirmado);"></i>Registrar pagamento</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="p-3 mb-3 rounded-3" style="background:var(--sabenta-bg);">
    <div style="font-size:.875rem;font-weight:600;">Fernanda Lima — Atendimento Individual</div>
    <div style="font-size:.8rem;color:var(--sabenta-text-muted);margin-top:.2rem;">08/04/2025 · 50 min · Valor: R$ 200,00</div>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Forma de pagamento</label>
        <select class="form-select">
            <option>Pix</option><option>Dinheiro</option><option>Transferência</option>
            <option>Cartão de débito</option><option>Cartão de crédito</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Valor recebido</label>
        <div class="input-group"><span class="input-group-text">R$</span>
        <input type="number" class="form-control" value="200.00"></div>
    </div>
    <div class="col-12">
        <label class="form-label">Data do pagamento</label>
        <input type="date" class="form-control" value="2025-04-17">
    </div>
    <div class="col-12">
        <label class="form-label">Observação</label>
        <input type="text" class="form-control" placeholder="Referência, número da transferência, etc...">
    </div>
</div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Confirmar pagamento</button>
</div>
</div></div></div>

{{-- Modal 5-B: Enviar cobrança --}}
<div class="modal fade" id="modalCobranca" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-lg">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title"><i class="bi bi-send me-2" style="color:var(--sabenta-primary);"></i>Enviar cobrança por WhatsApp</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body" x-data="{ msg: 'Olá Fernanda! Passando para lembrar sobre o valor de R$ 200,00 referente ao atendimento do dia 08/04. Quando puder, pode realizar o pagamento via Pix: 11.222.333/0001-44. Qualquer dúvida, fico à disposição! 😊 — Ana' }">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Mensagem de cobrança</label>
            <textarea class="form-control" rows="8" x-model="msg" style="font-size:.875rem;line-height:1.7;"></textarea>
            <div class="form-text"><i class="bi bi-info-circle me-1"></i>Edite a mensagem antes de enviar</div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Preview</label>
            <div style="background:#e5ddd5;border-radius:12px;padding:1rem;min-height:180px;">
                <div style="background:#fff;border-radius:8px 8px 8px 0;padding:.75rem 1rem;max-width:90%;font-size:.85rem;line-height:1.6;box-shadow:0 1px 2px rgba(0,0,0,.1);" x-text="msg"></div>
            </div>
            <div style="font-size:.775rem;color:var(--sabenta-text-muted);margin-top:.5rem;">
                <i class="bi bi-whatsapp me-1" style="color:#25D366;"></i>Será enviado para <strong>(11) 91234-5678</strong>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-success"><i class="bi bi-whatsapp me-1"></i>Enviar via WhatsApp</button>
</div>
</div></div></div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('graficoReceita'), {
  type: 'bar',
  data: {
    labels: ['Semana 1 (1–7)', 'Semana 2 (8–14)', 'Semana 3 (15–21)', 'Semana 4 (22–30)'],
    datasets: [{
      label: 'Receita (R$)',
      data: [950, 1100, 800, 550],
      backgroundColor: 'rgba(30,91,173,0.15)',
      borderColor: 'rgba(30,91,173,0.8)',
      borderWidth: 2,
      borderRadius: 8,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: (c) => 'R$ ' + c.parsed.y } }
    },
    scales: {
      y: { ticks: { callback: (v) => 'R$ ' + v }, grid: { color: 'rgba(221,228,240,0.5)' } },
      x: { grid: { display: false } }
    }
  }
});
</script>
@endpush

@endsection
