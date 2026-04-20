@extends('layouts.app')
@section('title', 'Editor — Minha Página')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item active">Minha Página</li>
</ol></nav>
@endsection
@section('content')

<div x-data="{ alterado: false, publicada: true }" @change.window="alterado = true">

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div>
        <h1 class="page-header__title">Minha página pública</h1>
        <div class="page-header__sub">
            <a href="{{ route('perfil.publico', 'ana-souza') }}" target="_blank" style="color:var(--sabenta-primary);">
                <i class="bi bi-box-arrow-up-right me-1"></i>sabenta.com/p/ana-souza
            </a>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span x-show="alterado" class="d-flex align-items-center gap-1" style="font-size:.8rem;color:var(--status-pendente);font-family:var(--font-heading);font-weight:600;">
            <i class="bi bi-dot" style="font-size:1.5rem;margin:-4px;"></i>Alterações não salvas
        </span>
        <button class="btn btn-outline-secondary btn-sm" @click="window.open('{{ route('perfil.publico', 'ana-souza') }}', '_blank')">
            <i class="bi bi-eye me-1"></i>Visualizar
        </button>
        <button class="btn btn-primary" @click="alterado = false">
            <i class="bi bi-floppy me-1"></i>Salvar alterações
        </button>
    </div>
</div>

<div class="row g-3">
    {{-- Editor --}}
    <div class="col-lg-5">
    <div class="d-flex flex-column gap-3">

        {{-- Status --}}
        <div class="sabenta-card">
        <div class="accordion accordion-flush" id="accStatus">
        <div class="accordion-item" style="border:none;">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#colStatus"
                        style="font-family:var(--font-heading);font-weight:700;font-size:.9375rem;border-radius:14px !important;box-shadow:none;color:var(--sabenta-text);">
                    <i class="bi bi-toggle-on me-2" style="color:var(--sabenta-primary);"></i>Status da página
                </button>
            </h2>
            <div id="colStatus" class="accordion-collapse collapse show">
            <div class="accordion-body" style="padding:1rem 1.375rem;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div style="font-size:.875rem;font-weight:600;" x-text="publicada ? 'Página publicada' : 'Página oculta'"></div>
                        <div style="font-size:.8rem;color:var(--sabenta-text-muted);" x-text="publicada ? 'Visível para qualquer pessoa com o link' : 'Não aparece publicamente'"></div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" x-model="publicada" style="width:2.5em;height:1.3em;cursor:pointer;">
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        </div>

        {{-- Perfil --}}
        <div class="sabenta-card">
        <div class="accordion accordion-flush">
        <div class="accordion-item" style="border:none;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colPerfil"
                        style="font-family:var(--font-heading);font-weight:700;font-size:.9375rem;box-shadow:none;color:var(--sabenta-text);">
                    <i class="bi bi-person-circle me-2" style="color:var(--sabenta-primary);"></i>Foto e perfil
                </button>
            </h2>
            <div id="colPerfil" class="accordion-collapse collapse">
            <div class="accordion-body" style="padding:1rem 1.375rem;">
                <div class="text-center mb-3">
                    <div class="sabenta-avatar avatar-lg mx-auto mb-2" style="width:72px;height:72px;font-size:1.375rem;border-radius:18px;">AS</div>
                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-camera me-1"></i>Alterar foto</button>
                </div>
                <div class="mb-3"><label class="form-label">Nome exibido</label>
                <input type="text" class="form-control" value="Dra. Ana Souza"></div>
                <div class="mb-3"><label class="form-label">Especialidade</label>
                <input type="text" class="form-control" value="Psicóloga Clínica · CRP 06/123456"></div>
                <div><label class="form-label">Bio (exibida na página)</label>
                <textarea class="form-control" rows="4" style="font-size:.875rem;line-height:1.65;">Psicóloga clínica com 8 anos de experiência. Atendo adultos e casais com foco em TCC e abordagem psicodinâmica.</textarea></div>
            </div>
            </div>
        </div>
        </div>
        </div>

        {{-- Especialidades --}}
        <div class="sabenta-card">
        <div class="accordion accordion-flush">
        <div class="accordion-item" style="border:none;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colEsp"
                        style="font-family:var(--font-heading);font-weight:700;font-size:.9375rem;box-shadow:none;color:var(--sabenta-text);">
                    <i class="bi bi-tags me-2" style="color:var(--sabenta-primary);"></i>Especialidades
                </button>
            </h2>
            <div id="colEsp" class="accordion-collapse collapse">
            <div class="accordion-body" style="padding:1rem 1.375rem;">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach(['Ansiedade','Depressão','TCC','Relacionamentos','Autoestima','Luto','Trauma','Burnout'] as $tag)
                    <span class="sabenta-badge badge-realizado" style="cursor:pointer;user-select:none;font-size:.8rem;">{{ $tag }} <i class="bi bi-x-sm ms-1"></i></span>
                    @endforeach
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Adicionar especialidade...">
                    <button class="btn btn-primary"><i class="bi bi-plus-lg"></i></button>
                </div>
            </div>
            </div>
        </div>
        </div>
        </div>

        {{-- Aparência --}}
        <div class="sabenta-card">
        <div class="accordion accordion-flush">
        <div class="accordion-item" style="border:none;">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colApar"
                        style="font-family:var(--font-heading);font-weight:700;font-size:.9375rem;box-shadow:none;color:var(--sabenta-text);">
                    <i class="bi bi-palette me-2" style="color:var(--sabenta-primary);"></i>Aparência
                </button>
            </h2>
            <div id="colApar" class="accordion-collapse collapse">
            <div class="accordion-body" style="padding:1rem 1.375rem;">
                <label class="form-label">Cor de destaque</label>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach(['#1E5BAD','#8B5CF6','#1D9E75','#D97706','#DC2626','#0F172A'] as $cor)
                    <div style="width:32px;height:32px;border-radius:8px;background:{{ $cor }};cursor:pointer;border:2px solid {{ $cor === '#1E5BAD' ? '#fff' : 'transparent' }};box-shadow:{{ $cor === '#1E5BAD' ? '0 0 0 2px '.$cor : 'none' }};transition:transform .15s;" onclick=""></div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <label class="form-label">Cor personalizada</label>
                    <div class="input-group"><input type="color" class="form-control form-control-color" value="#1E5BAD" style="width:60px;padding:.35rem;">
                    <input type="text" class="form-control" value="#1E5BAD" style="max-width:120px;"></div>
                </div>
            </div>
            </div>
        </div>
        </div>
        </div>

        {{-- Seções visíveis --}}
        <div class="sabenta-card">
        <div class="card-header"><span class="card-header-title"><i class="bi bi-eye me-2" style="color:var(--sabenta-primary);"></i>Seções visíveis</span></div>
        <div class="card-body">
        @foreach([['Sobre mim','Texto de apresentação'],['Especialidades','Tags com suas áreas de atuação'],['Tipos de sessão','Serviços e valores'],['Calendário','Horários disponíveis para agendar'],['Política de cancelamento','Suas regras de cancelamento']] as $sec)
        <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--sabenta-border);">
            <div><div style="font-size:.875rem;font-weight:600;">{{ $sec[0] }}</div>
            <div style="font-size:.78rem;color:var(--sabenta-text-muted);">{{ $sec[1] }}</div></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked style="cursor:pointer;"></div>
        </div>
        @endforeach
        </div>
        </div>

    </div>
    </div>

    {{-- Preview --}}
    <div class="col-lg-7">
    <div class="sabenta-card" style="overflow:hidden;position:sticky;top:calc(var(--topbar-height) + 1.5rem);">
    <div class="card-header">
        <span class="card-header-title"><i class="bi bi-laptop me-2"></i>Preview</span>
        <div style="font-size:.8rem;color:var(--sabenta-text-muted);">Atualizado em tempo real</div>
    </div>
    <div style="background:#f5f5f5;padding:.75rem;border-radius:0 0 14px 14px;min-height:600px;overflow:hidden;">
        {{-- Simula a página --}}
        <div style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,.1);transform:scale(.95);transform-origin:top center;">
            <div style="height:6px;background:var(--sabenta-primary);"></div>
            <div style="padding:2rem 1.5rem;text-align:center;background:linear-gradient(135deg,rgba(30,91,173,.05) 0%,rgba(99,179,245,.06) 100%);">
                <div class="sabenta-avatar mx-auto mb-2" style="width:72px;height:72px;font-size:1.375rem;border-radius:50%;">AS</div>
                <h3 style="font-family:var(--font-heading);font-size:1.25rem;font-weight:800;color:var(--sabenta-text);margin-bottom:.3rem;">Dra. Ana Souza</h3>
                <p style="font-size:.8rem;color:var(--sabenta-text-muted);margin-bottom:1rem;">Psicóloga Clínica · CRP 06/123456</p>
                <a href="#" class="btn btn-primary btn-sm"><i class="bi bi-calendar-check me-1"></i>Agendar consulta</a>
            </div>
            <div style="padding:1.25rem 1.5rem;">
                <h4 style="font-family:var(--font-heading);font-size:.875rem;font-weight:700;color:var(--sabenta-text);margin-bottom:.5rem;">Sobre mim</h4>
                <p style="font-size:.8rem;color:var(--sabenta-text-muted);line-height:1.6;">Psicóloga clínica com 8 anos de experiência. Atendo adultos e casais com foco em TCC e abordagem psicodinâmica.</p>
                <h4 style="font-family:var(--font-heading);font-size:.875rem;font-weight:700;color:var(--sabenta-text);margin:.75rem 0 .5rem;">Especialidades</h4>
                <div class="d-flex flex-wrap gap-1">
                    @foreach(['Ansiedade','Depressão','TCC','Relacionamentos','Autoestima'] as $t)
                    <span style="background:rgba(30,91,173,.08);color:var(--sabenta-primary);padding:.2rem .7rem;border-radius:20px;font-size:.7rem;font-weight:600;">{{ $t }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
</div>

</div>

@endsection
