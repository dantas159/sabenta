@extends('layouts.public')

@section('title', 'Planos e Preços — Sabenta')

@push('styles')
<style>
    .plan-hero {
        background: linear-gradient(155deg, #0f2460 0%, #1a3578 100%);
        padding: 4rem 0 6rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .plan-hero::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 70px;
        background: var(--sabenta-bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .billing-toggle {
        display: inline-flex;
        align-items: center;
        background: rgba(255,255,255,0.1);
        border-radius: 999px;
        padding: 0.375rem;
        gap: 0.25rem;
        margin-top: 1.75rem;
    }
    .billing-btn {
        padding: 0.5rem 1.25rem;
        border-radius: 999px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: rgba(255,255,255,0.6);
    }
    .billing-btn.active { background: #fff; color: var(--sabenta-primary); }
    .save-badge {
        background: var(--sabenta-accent);
        color: var(--sabenta-primary-dark);
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.2rem 0.625rem;
        margin-left: 0.5rem;
        vertical-align: middle;
    }
    .plan-card {
        border-radius: 20px;
        padding: 2rem;
        background: #fff;
        border: 2px solid var(--sabenta-border);
        height: 100%;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .plan-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(15,36,96,0.13); }
    .plan-card.popular {
        border-color: var(--sabenta-primary);
        background: linear-gradient(165deg, #f0f6ff 0%, #fff 100%);
        box-shadow: 0 8px 40px rgba(26,53,120,0.15);
    }
    .popular-badge {
        position: absolute;
        top: -13px; left: 50%; transform: translateX(-50%);
        background: var(--sabenta-primary);
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        border-radius: 999px;
        padding: 0.25rem 1rem;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .plan-name { font-family:var(--font-heading);font-weight:800;font-size:1.125rem;margin-bottom:0.25rem; }
    .plan-price {
        font-family:var(--font-heading);font-weight:900;
        font-size:2.5rem;line-height:1;color:var(--sabenta-primary);
        margin: 1rem 0 0.25rem;
    }
    .plan-price sup { font-size:1.25rem;vertical-align:top;margin-top:0.4rem; }
    .plan-price-per { font-size:0.8rem;color:var(--sabenta-text-muted);font-weight:400; }
    .plan-feature { display:flex;align-items:flex-start;gap:0.625rem;margin-bottom:0.75rem; }
    .plan-feature i { color:var(--sabenta-success);margin-top:0.1rem;flex-shrink:0; }
    .plan-feature span { font-size:0.875rem; }

    .compare-table th { font-family:var(--font-heading);font-size:0.8125rem;font-weight:700;padding:0.875rem 1rem;background:var(--sabenta-primary-light); }
    .compare-table td { padding:0.75rem 1rem;font-size:0.875rem;vertical-align:middle; }
    .compare-table .feature-name { color:var(--sabenta-text);font-weight:500; }
    .check { color:var(--sabenta-success);font-size:1.1rem; }
    .cross { color:var(--sabenta-border);font-size:1rem; }
    .faq-item { border:none;border-bottom:1px solid var(--sabenta-border);border-radius:0!important; }
    .faq-item:first-child { border-top:1px solid var(--sabenta-border); }
</style>
@endpush

@section('content')

<div x-data="{ anual: false }">

{{-- Hero --}}
<div class="plan-hero">
    <div class="container text-center">
        <h1 style="font-family:var(--font-heading);font-size:2.25rem;font-weight:900;margin-bottom:0.75rem;">
            Planos e preços
        </h1>
        <p style="color:rgba(255,255,255,0.65);font-size:1rem;max-width:420px;margin:0 auto;">
            Sem contratos longos. Cancele quando quiser. Comece grátis por 14 dias.
        </p>
        <div class="billing-toggle">
            <button class="billing-btn" :class="{ active: !anual }" @click="anual = false">Mensal</button>
            <button class="billing-btn" :class="{ active: anual }" @click="anual = true">
                Anual <span class="save-badge">-20%</span>
            </button>
        </div>
    </div>
</div>

{{-- Planos --}}
<div class="container" style="margin-top:-2.5rem;position:relative;z-index:2;">
    <div class="row g-4 justify-content-center mb-5">

        {{-- Solo --}}
        <div class="col-md-4 col-lg-3">
            <div class="plan-card">
                <div class="plan-name">Solo</div>
                <p style="font-size:0.8125rem;color:var(--sabenta-text-muted);">Para profissionais autônomos</p>
                <div class="plan-price">
                    <sup>R$</sup>
                    <span x-text="anual ? '63' : '79'"></span>
                    <span class="plan-price-per">/mês</span>
                </div>
                <p style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;" x-show="anual">
                    R$ 756/ano — economize R$ 192
                </p>
                <p style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;" x-show="!anual">
                    &nbsp;
                </p>
                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mb-3">Começar grátis</a>
                @foreach(['1 profissional','Até 60 pacientes','Agenda online 24h','Lembretes WhatsApp','Página de perfil','Gestão financeira básica'] as $f)
                <div class="plan-feature">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Clínica Pequena --}}
        <div class="col-md-4 col-lg-3">
            <div class="plan-card popular">
                <div class="popular-badge">Mais popular</div>
                <div class="plan-name" style="color:var(--sabenta-primary);">Clínica Pequena</div>
                <p style="font-size:0.8125rem;color:var(--sabenta-text-muted);">Para clínicas em crescimento</p>
                <div class="plan-price">
                    <sup>R$</sup>
                    <span x-text="anual ? '159' : '199'"></span>
                    <span class="plan-price-per">/mês</span>
                </div>
                <p style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;" x-show="anual">
                    R$ 1.908/ano — economize R$ 480
                </p>
                <p style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;" x-show="!anual">
                    &nbsp;
                </p>
                <a href="{{ route('register') }}" class="btn btn-primary w-100 mb-3">Começar grátis</a>
                @foreach(['Até 5 profissionais','Pacientes ilimitados','Tudo do plano Solo','Painel da clínica','Relatórios avançados','Suporte prioritário','Múltiplas salas/locais'] as $f)
                <div class="plan-feature">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Clínica Média --}}
        <div class="col-md-4 col-lg-3">
            <div class="plan-card">
                <div class="plan-name">Clínica Média</div>
                <p style="font-size:0.8125rem;color:var(--sabenta-text-muted);">Para clínicas consolidadas</p>
                <div class="plan-price">
                    <sup>R$</sup>
                    <span x-text="anual ? '279' : '349'"></span>
                    <span class="plan-price-per">/mês</span>
                </div>
                <p style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;" x-show="anual">
                    R$ 3.348/ano — economize R$ 840
                </p>
                <p style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:1.5rem;" x-show="!anual">
                    &nbsp;
                </p>
                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mb-3">Começar grátis</a>
                @foreach(['Até 20 profissionais','Pacientes ilimitados','Tudo do plano Clínica','API de integração','Gestor de receita','Gerente de conta dedicado','SLA de suporte'] as $f)
                <div class="plan-feature">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ $f }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

{{-- Tabela comparativa --}}
<div class="container mb-5">
    <h3 style="font-family:var(--font-heading);font-weight:800;font-size:1.5rem;text-align:center;margin-bottom:2rem;">
        Comparativo completo
    </h3>
    <div class="table-responsive">
        <table class="compare-table table table-bordered" style="border-radius:14px;overflow:hidden;">
            <thead>
                <tr>
                    <th style="width:40%;">Funcionalidade</th>
                    <th class="text-center">Solo</th>
                    <th class="text-center" style="background:var(--sabenta-primary-light);color:var(--sabenta-primary);">Clínica P.</th>
                    <th class="text-center">Clínica M.</th>
                </tr>
            </thead>
            <tbody>
                @php
                $rows = [
                    ['Agenda online 24h', true, true, true],
                    ['Lembretes WhatsApp', true, true, true],
                    ['Página de perfil público', true, true, true],
                    ['Gestão financeira', 'Básica', 'Completa', 'Completa'],
                    ['Múltiplos profissionais', false, '5', '20'],
                    ['Painel da clínica', false, true, true],
                    ['Relatórios avançados', false, true, true],
                    ['API de integração', false, false, true],
                    ['Gerente de conta', false, false, true],
                    ['Suporte', 'E-mail', 'Prioritário', 'Dedicado'],
                ];
                @endphp
                @foreach($rows as [$feat, $s, $cp, $cm])
                <tr>
                    <td class="feature-name">{{ $feat }}</td>
                    @foreach([$s, $cp, $cm] as $val)
                    <td class="text-center">
                        @if($val === true)
                            <i class="bi bi-check-circle-fill check"></i>
                        @elseif($val === false)
                            <i class="bi bi-x-circle cross"></i>
                        @else
                            {{ $val }}
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- FAQ --}}
<div class="container mb-5" style="max-width:700px;">
    <h3 style="font-family:var(--font-heading);font-weight:800;font-size:1.5rem;text-align:center;margin-bottom:2rem;">
        Perguntas frequentes
    </h3>
    <div class="accordion" id="faqAcordeon">
        @foreach([
            ['Como funciona o período grátis?','Você tem 14 dias para testar todos os recursos sem precisar cadastrar cartão de crédito. Ao final, escolhe o plano que melhor se encaixa.'],
            ['Posso mudar de plano depois?','Sim, a qualquer momento. O upgrade é imediato e o crédito do mês anterior é abatido automaticamente.'],
            ['Meus dados são seguros?','Todos os dados são criptografados em trânsito e em repouso. Cada clínica tem seu ambiente isolado.'],
            ['Como funciona o cancelamento?','Você pode cancelar a qualquer momento pelo painel, sem multa. Seu acesso permanece até o fim do período pago.'],
            ['Preciso instalar alguma coisa?','Não. O Sabenta é 100% online, acessível de qualquer dispositivo com navegador.'],
        ] as $i => [$q, $r])
        <div class="accordion-item faq-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                        data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                    {{ $q }}
                </button>
            </h2>
            <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}" data-bs-parent="#faqAcordeon">
                <div class="accordion-body" style="font-size:0.9rem;color:var(--sabenta-text-muted);line-height:1.7;">
                    {{ $r }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

</div>{{-- x-data --}}
@endsection
