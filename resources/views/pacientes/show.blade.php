@extends('layouts.app')
@section('title', 'Perfil do Paciente')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item"><a href="{{ route('painel.pacientes.index') }}">Pacientes</a></li>
<li class="breadcrumb-item active">Mariana Costa</li>
</ol></nav>
@endsection
@section('content')

<div class="row g-3">

    {{-- Ficha lateral --}}
    <div class="col-lg-4">
        <div class="sabenta-card">
        <div class="card-body text-center" style="padding:2rem 1.5rem;">
            <div class="sabenta-avatar avatar-lg mx-auto mb-3" style="width:72px;height:72px;font-size:1.375rem;border-radius:18px;background:var(--sabenta-primary);">MC</div>
            <h2 style="font-family:var(--font-heading);font-size:1.125rem;font-weight:700;margin-bottom:.25rem;">Mariana Costa</h2>
            <p style="font-size:.8375rem;color:var(--sabenta-text-muted);margin-bottom:1.25rem;">Paciente desde Janeiro/2024</p>
            <div class="d-flex gap-2 justify-content-center mb-3">
                <span class="sabenta-badge badge-confirmado">Ativa</span>
            </div>
            <div class="d-grid gap-2">
                <a href="{{ route('painel.agenda.dia') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-calendar-plus me-1"></i>Agendar sessão
                </a>
            </div>
        </div>
        <div style="border-top:1px solid var(--sabenta-border);padding:1.25rem;">
            <div class="row g-3 text-center mb-3">
                @foreach([['Total sessões','24','bi-calendar3','var(--sabenta-primary)'],['Realizadas','21','bi-check-circle','var(--status-confirmado)'],['Faltas','2','bi-x-circle','var(--status-faltou)'],['Investido','R$ 4.200','bi-cash-coin','var(--status-pendente)']] as $s)
                <div class="col-6">
                    <div style="background:var(--sabenta-bg);border-radius:10px;padding:.75rem .5rem;text-align:center;">
                        <i class="bi {{ $s[2] }}" style="color:{{ $s[3] }};font-size:1.125rem;"></i>
                        <div style="font-family:var(--font-heading);font-weight:700;font-size:1rem;color:var(--sabenta-text);margin-top:.3rem;">{{ $s[1] }}</div>
                        <div style="font-size:.7rem;color:var(--sabenta-text-muted);">{{ $s[0] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="font-size:.8rem;line-height:2;">
                <div class="d-flex justify-content-between"><span style="color:var(--sabenta-text-muted);">WhatsApp</span><span style="font-weight:500;">(11) 99211-4455</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--sabenta-text-muted);">E-mail</span><span style="font-weight:500;">mari@email.com</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--sabenta-text-muted);">Nascimento</span><span style="font-weight:500;">12/05/1990 (34 anos)</span></div>
                <div class="d-flex justify-content-between"><span style="color:var(--sabenta-text-muted);">Como chegou</span><span style="font-weight:500;">Indicação</span></div>
            </div>
        </div>
        <div style="border-top:1px solid var(--sabenta-border);padding:.875rem 1.25rem;">
            <button class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#modalExcluir">
                <i class="bi bi-trash me-1"></i>Excluir paciente
            </button>
        </div>
        </div>
    </div>

    {{-- Conteúdo com abas --}}
    <div class="col-lg-8">
        <div class="sabenta-card">
        <div class="card-header" style="padding:0 1.375rem;">
            <ul class="nav nav-tabs border-0" id="pacienteTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#historico">
                    <i class="bi bi-calendar-check me-1"></i>Histórico
                </button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#proximas">
                    <i class="bi bi-calendar-event me-1"></i>Próximas
                </button></li>
            </ul>
        </div>
        <div class="tab-content">
            {{-- Histórico --}}
            <div class="tab-pane fade show active" id="historico">
            <div style="overflow-x:auto;">
            @php
            $hist = [
                ['17/04/2025','09:00','Consulta Individual','realizado','R$ 200','Pix'],
                ['10/04/2025','09:00','Consulta Individual','realizado','R$ 200','Pix'],
                ['03/04/2025','09:00','Consulta Individual','faltou','R$ 200','—'],
                ['27/03/2025','09:00','Consulta Individual','realizado','R$ 200','Transferência'],
                ['20/03/2025','09:00','Consulta Individual','realizado','R$ 200','Pix'],
            ];
            @endphp
            <table class="sabenta-table">
            <thead><tr><th>Data</th><th>Serviço</th><th>Status</th><th>Valor</th><th>Pagamento</th><th></th></tr></thead>
            <tbody>
            @foreach($hist as $h)
            <tr>
                <td style="font-weight:600;font-size:.875rem;">{{ $h[0] }} <span style="color:var(--sabenta-text-muted);font-weight:400;">{{ $h[1] }}</span></td>
                <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $h[2] }}</td>
                <td><x-badge-status :status="$h[3]" /></td>
                <td style="font-weight:600;font-size:.875rem;">{{ $h[4] }}</td>
                <td style="font-size:.8375rem;color:var(--sabenta-text-muted);">{{ $h[5] }}</td>
                <td><button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;"><i class="bi bi-eye"></i></button></td>
            </tr>
            @endforeach
            </tbody>
            </table>
            </div>
            </div>

            {{-- Próximas --}}
            <div class="tab-pane fade" id="proximas">
            <div style="padding:1.25rem;">
                @php
                $proximas = [
                    ['24/04/2025','Qui','09:00','Consulta Individual','50 min'],
                    ['01/05/2025','Qui','09:00','Consulta Individual','50 min'],
                    ['08/05/2025','Qui','09:00','Consulta Individual','50 min'],
                ];
                @endphp
                @foreach($proximas as $p)
                <div class="d-flex align-items-center justify-content-between p-3 mb-2" style="background:var(--sabenta-bg);border-radius:10px;border:1px solid var(--sabenta-border);">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:44px;height:44px;border-radius:10px;background:rgba(30,91,173,.1);display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <div style="font-size:.65rem;color:var(--sabenta-primary);font-family:var(--font-heading);font-weight:700;text-transform:uppercase;">{{ $p[1] }}</div>
                            <div style="font-size:1rem;color:var(--sabenta-primary);font-family:var(--font-heading);font-weight:800;line-height:1.1;">{{ substr($p[0],0,2) }}</div>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:.875rem;">{{ $p[2] }} — {{ $p[3] }}</div>
                            <div style="font-size:.8rem;color:var(--sabenta-text-muted);">{{ $p[4] }} · {{ $p[0] }}</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" style="padding:.25rem .55rem;"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" style="padding:.25rem .55rem;"><i class="bi bi-x-lg"></i></button>
                    </div>
                </div>
                @endforeach
            </div>
            </div>
        </div>
        </div>
    </div>
</div>

{{-- Modal 4-C: Excluir --}}
<div class="modal fade" id="modalExcluir" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title text-danger"><i class="bi bi-trash me-2"></i>Excluir paciente</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="p-3 mb-3 rounded-3" style="background:rgba(220,38,38,.06);border:1px solid rgba(220,38,38,.2);">
        <strong style="font-size:.875rem;color:var(--status-faltou);">Atenção: esta ação é irreversível.</strong>
        <p style="font-size:.8rem;color:var(--sabenta-text-muted);margin-top:.35rem;margin-bottom:0;">Todo o histórico e sessões de <strong>Mariana Costa</strong> serão permanentemente excluídos.</p>
    </div>
    <label class="form-label" style="font-size:.875rem;">Para confirmar, digite <strong>EXCLUIR</strong> abaixo:</label>
    <input type="text" class="form-control" placeholder="EXCLUIR" x-data x-bind:class="$el.value === 'EXCLUIR' ? 'is-valid' : ''">
</div>
<div class="modal-footer">
    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-danger"><i class="bi bi-trash me-1"></i>Excluir permanentemente</button>
</div>
</div></div></div>

@endsection
