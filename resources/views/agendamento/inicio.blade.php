@extends('layouts.public')

@section('title', 'Agendar com ' . ($profissional->nome ?? 'Profissional'))

@push('styles')
<style>
    .booking-hero {
        background: linear-gradient(135deg, var(--sabenta-primary) 0%, var(--sabenta-primary-dark) 100%);
        padding: 3rem 0 5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .booking-hero::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 60px;
        background: var(--sabenta-bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .booking-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.4);
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        font-family: var(--font-heading);
    }
    .booking-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 2.5rem;
        margin-top: -2rem;
        position: relative;
        z-index: 2;
    }
    .booking-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.375rem;
        position: relative;
    }
    .booking-step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 16px;
        left: calc(50% + 20px);
        width: calc(100% - 8px);
        height: 2px;
        background: var(--sabenta-border);
    }
    .booking-step.done::after { background: var(--sabenta-primary); }
    .booking-step__num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid var(--sabenta-border);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--sabenta-text-muted);
        font-family: var(--font-heading);
        position: relative;
        z-index: 1;
    }
    .booking-step.active .booking-step__num {
        background: var(--sabenta-primary);
        border-color: var(--sabenta-primary);
        color: #fff;
    }
    .booking-step.done .booking-step__num {
        background: var(--sabenta-success);
        border-color: var(--sabenta-success);
        color: #fff;
    }
    .booking-step__label {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--sabenta-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .booking-step.active .booking-step__label { color: var(--sabenta-primary); }
    .booking-step.done .booking-step__label { color: var(--sabenta-success); }
    .service-card {
        border: 2px solid var(--sabenta-border);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        cursor: pointer;
        transition: all 0.18s ease;
        background: #fff;
    }
    .service-card:hover { border-color: var(--sabenta-primary); background: var(--sabenta-primary-light); }
    .service-card.selected { border-color: var(--sabenta-primary); background: var(--sabenta-primary-light); }
    .service-card.selected .service-card__name { color: var(--sabenta-primary); }
</style>
@endpush

@section('content')
{{-- Hero --}}
<div class="booking-hero">
    <div class="container text-center">
        <div class="booking-avatar mx-auto mb-3">
            @if(!empty($profissional->foto))
                <img src="{{ asset('storage/' . $profissional->foto) }}" alt="{{ $profissional->nome ?? 'Profissional' }}"
                     style="width:96px;height:96px;border-radius:50%;object-fit:cover;">
            @else
                {{ mb_substr($profissional->nome ?? 'P', 0, 1) }}
            @endif
        </div>
        <h1 style="font-family:var(--font-heading);font-size:1.625rem;font-weight:800;margin-bottom:0.375rem;">
            {{ $profissional->nome ?? 'Dra. Mariana Costa' }}
        </h1>
        <p style="color:rgba(255,255,255,0.7);font-size:0.9rem;margin-bottom:0;">
            {{ $profissional->especialidade ?? 'Psicóloga Clínica' }}
            @if(!empty($profissional->crp))
                &nbsp;·&nbsp; CRP {{ $profissional->crp }}
            @endif
        </p>
    </div>
</div>

{{-- Steps --}}
<div class="container" style="max-width:640px;">
    <div class="d-flex booking-steps pt-4">
        <div class="booking-step active flex-fill">
            <div class="booking-step__num">1</div>
            <span class="booking-step__label">Serviço</span>
        </div>
        <div class="booking-step flex-fill">
            <div class="booking-step__num">2</div>
            <span class="booking-step__label">Horário</span>
        </div>
        <div class="booking-step flex-fill">
            <div class="booking-step__num">3</div>
            <span class="booking-step__label">Seus dados</span>
        </div>
        <div class="booking-step flex-fill">
            <div class="booking-step__num">4</div>
            <span class="booking-step__label">Confirmação</span>
        </div>
    </div>

    {{-- Conteúdo --}}
    <div class="sabenta-card p-4 mb-4">
        <h5 style="font-family:var(--font-heading);font-weight:700;margin-bottom:0.25rem;">Escolha o serviço</h5>
        <p class="text-muted" style="font-size:0.875rem;margin-bottom:1.5rem;">Selecione o tipo de consulta ou sessão</p>

        <div class="d-flex flex-column gap-3">
            @php
            $servicos = $servicos ?? [
                ['id'=>1,'nome'=>'Consulta de Avaliação','duracao'=>60,'valor'=>250],
                ['id'=>2,'nome'=>'Sessão de Psicoterapia','duracao'=>50,'valor'=>200],
                ['id'=>3,'nome'=>'Psicoterapia Online','duracao'=>50,'valor'=>180],
            ];
            @endphp

            @foreach($servicos as $s)
            <a href="{{ route('agendar.horario', ['slug' => $slug ?? 'profissional']) }}?servico={{ $s['id'] }}"
               class="service-card text-decoration-none d-flex justify-content-between align-items-center">
                <div>
                    <div class="service-card__name fw-600" style="font-family:var(--font-heading);color:var(--sabenta-text);font-size:0.9375rem;">
                        {{ $s['nome'] }}
                    </div>
                    <div style="font-size:0.8125rem;color:var(--sabenta-text-muted);margin-top:0.2rem;">
                        <i class="bi bi-clock me-1"></i>{{ $s['duracao'] }} min
                    </div>
                </div>
                <div class="text-end">
                    <div style="font-size:1rem;font-weight:700;color:var(--sabenta-primary);font-family:var(--font-heading);">
                        R$&nbsp;{{ number_format($s['valor'], 2, ',', '.') }}
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--sabenta-text-muted);font-size:0.75rem;"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <p class="text-center text-muted" style="font-size:0.775rem;margin-bottom:3rem;">
        <i class="bi bi-shield-lock me-1"></i>
        Seus dados são protegidos com criptografia e em conformidade com a LGPD.
    </p>
</div>
@endsection
