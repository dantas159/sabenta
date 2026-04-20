@extends('layouts.app')
@section('title', 'Anotações — Mariana Costa')
@section('breadcrumb')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
<li class="breadcrumb-item"><a href="{{ route('painel.dashboard') }}">Painel</a></li>
<li class="breadcrumb-item"><a href="{{ route('painel.pacientes.index') }}">Pacientes</a></li>
<li class="breadcrumb-item"><a href="{{ route('painel.pacientes.show') }}">Mariana Costa</a></li>
<li class="breadcrumb-item active">Anotações</li>
</ol></nav>
@endsection
@section('content')

<div class="row g-3" x-data="{ status: 'saved', content: '' }">
    <div class="col-12">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h1 class="page-header__title">Anotações privadas</h1>
                <div class="page-header__sub">
                    <a href="{{ route('painel.pacientes.show') }}">Mariana Costa</a>
                    <span class="separator">/</span>
                    Anotações clínicas
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div x-show="status === 'saved'" style="display:none;" class="d-flex align-items-center gap-1" style="font-size:.8rem;color:var(--status-confirmado);">
                    <i class="bi bi-check-circle-fill"></i>
                    <span style="font-family:var(--font-heading);font-weight:600;font-size:.8rem;">Salvo</span>
                </div>
                <div x-show="status === 'saving'" style="display:none;" class="d-flex align-items-center gap-1" style="font-size:.8rem;color:var(--status-pendente);">
                    <div class="spinner-border spinner-border-sm" style="width:.75rem;height:.75rem;border-width:2px;color:var(--status-pendente);"></div>
                    <span style="font-family:var(--font-heading);font-weight:600;font-size:.8rem;color:var(--status-pendente);">Salvando...</span>
                </div>
                <button class="btn btn-primary" @click="status = 'saving'; setTimeout(() => status = 'saved', 1500)">
                    <i class="bi bi-floppy me-1"></i>Salvar
                </button>
            </div>
        </div>

        {{-- Aviso de privacidade --}}
        <div class="d-flex align-items-start gap-3 p-4 mb-3 rounded-3" style="background:rgba(30,91,173,.05);border:1.5px solid rgba(30,91,173,.15);">
            <i class="bi bi-shield-lock-fill" style="color:var(--sabenta-primary);font-size:1.375rem;flex-shrink:0;"></i>
            <div>
                <div style="font-family:var(--font-heading);font-weight:700;font-size:.9375rem;color:var(--sabenta-primary);margin-bottom:.3rem;">
                    Suas anotações são 100% privadas e criptografadas
                </div>
                <div style="font-size:.8375rem;color:var(--sabenta-text-muted);line-height:1.6;">
                    Este espaço é exclusivo para suas anotações clínicas. Os dados são criptografados em trânsito e em repouso.
                    Nem a equipe da Sabenta consegue acessar este conteúdo. Em conformidade com o CFP e a LGPD.
                </div>
            </div>
        </div>

        <div class="sabenta-card">
        <div class="card-header">
            <span class="card-header-title"><i class="bi bi-journal-lock me-2" style="color:var(--sabenta-primary);"></i>Prontuário — Mariana Costa</span>
            <div style="font-size:.8rem;color:var(--sabenta-text-muted);">Última edição: 17/04/2025 às 10:32</div>
        </div>
        <div class="card-body" style="padding:0;">
            <textarea class="form-control"
                      rows="18"
                      style="border:none;border-radius:0 0 14px 14px;min-height:400px;font-size:.9375rem;line-height:1.8;padding:1.5rem;resize:vertical;"
                      placeholder="Digite suas anotações clínicas aqui...&#10;&#10;Este espaço é privado e seguro."
                      @input="status = 'saving'; clearTimeout(window._saveTimer); window._saveTimer = setTimeout(() => status = 'saved', 2000)">Primeira consulta (jan/2024): Paciente relata ansiedade crônica desde adolescência, associada a pressão familiar e acadêmica. Apresenta bom insight sobre seus padrões. Queixa principal: dificuldade de estabelecer limites interpessoais.

Sessão 3 (fev/2024): Abertura significativa sobre relação com figura materna. Presença de padrão de complacência compulsiva. Propor trabalho com assertividade.

Sessão 7 (mar/2024): Relatou episódio de conflito no trabalho onde conseguiu se posicionar. Progresso notável. Continuar reforçando autoconfiança.

Sessão 12 (jun/2024): Relata sonhos recorrentes com abandono. Possível material para trabalho com apego. Investigar histórico de rupturas afetivas.

Atualização (abr/2025): Boa evolução geral. Redução significativa dos episódios ansiosos. Paciente demonstra maior autonomia nas tomadas de decisão.</textarea>
        </div>
        </div>
    </div>
</div>

@endsection
