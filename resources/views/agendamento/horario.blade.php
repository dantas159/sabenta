@extends('layouts.public')

@section('title', 'Escolha o horário')

@push('styles')
<style>
    .booking-hero {
        background: linear-gradient(135deg, var(--sabenta-primary) 0%, var(--sabenta-primary-dark) 100%);
        padding: 2rem 0 4.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .booking-hero::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 60px;
        background: var(--sabenta-bg);
        clip-path: ellipse(55% 100% at 50% 100%);
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
    .booking-step { display:flex;flex-direction:column;align-items:center;gap:0.375rem;position:relative; }
    .booking-step:not(:last-child)::after {
        content:'';position:absolute;top:16px;left:calc(50% + 20px);
        width:calc(100% - 8px);height:2px;background:var(--sabenta-border);
    }
    .booking-step.done::after { background:var(--sabenta-primary); }
    .booking-step__num {
        width:32px;height:32px;border-radius:50%;border:2px solid var(--sabenta-border);
        background:#fff;display:flex;align-items:center;justify-content:center;
        font-size:0.8125rem;font-weight:700;color:var(--sabenta-text-muted);
        font-family:var(--font-heading);position:relative;z-index:1;
    }
    .booking-step.active .booking-step__num { background:var(--sabenta-primary);border-color:var(--sabenta-primary);color:#fff; }
    .booking-step.done .booking-step__num { background:var(--sabenta-success);border-color:var(--sabenta-success);color:#fff; }
    .booking-step__label { font-size:0.7rem;font-weight:600;color:var(--sabenta-text-muted);text-transform:uppercase;letter-spacing:0.04em;white-space:nowrap; }
    .booking-step.active .booking-step__label { color:var(--sabenta-primary); }
    .booking-step.done .booking-step__label { color:var(--sabenta-success); }

    .cal-nav { display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem; }
    .cal-nav__month { font-family:var(--font-heading);font-weight:700;font-size:0.9375rem; }
    .cal-grid { display:grid;grid-template-columns:repeat(7,1fr);gap:4px; }
    .cal-header { font-size:0.7rem;font-weight:600;color:var(--sabenta-text-muted);text-align:center;padding:0.25rem 0;text-transform:uppercase; }
    .cal-day {
        aspect-ratio:1;border-radius:8px;display:flex;align-items:center;justify-content:center;
        font-size:0.8125rem;font-weight:500;cursor:pointer;border:2px solid transparent;transition:all 0.15s;
    }
    .cal-day.vazio { opacity:0; pointer-events:none; }
    .cal-day.indisponivel { color:var(--sabenta-text-muted);pointer-events:none;opacity:0.45; }
    .cal-day.disponivel { color:var(--sabenta-text);background:var(--sabenta-primary-light); }
    .cal-day.disponivel:hover { border-color:var(--sabenta-primary);background:var(--sabenta-primary-light); }
    .cal-day.selecionado { background:var(--sabenta-primary);color:#fff;border-color:var(--sabenta-primary); }
    .cal-day.hoje { font-weight:800; }

    .time-slot {
        padding:0.5rem 0.875rem;border:2px solid var(--sabenta-border);border-radius:10px;
        font-size:0.875rem;font-weight:600;cursor:pointer;text-align:center;transition:all 0.15s;
        font-family:var(--font-heading);color:var(--sabenta-text);background:#fff;
    }
    .time-slot:hover { border-color:var(--sabenta-primary);color:var(--sabenta-primary); }
    .time-slot.selected { background:var(--sabenta-primary);border-color:var(--sabenta-primary);color:#fff; }
    .time-slot.busy { opacity:0.35;pointer-events:none;text-decoration:line-through; }
</style>
@endpush

@section('content')
<div class="booking-hero">
    <div class="container text-center">
        <p style="color:rgba(255,255,255,0.7);font-size:0.875rem;margin-bottom:0.25rem;">Agendando com</p>
        <h1 style="font-family:var(--font-heading);font-size:1.375rem;font-weight:800;margin-bottom:0;">
            {{ $profissional->nome ?? 'Dra. Mariana Costa' }}
        </h1>
    </div>
</div>

<div class="container" style="max-width:700px;" x-data="{
    mesSelecionado: 4,
    anoSelecionado: 2026,
    diaSelecionado: null,
    horarioSelecionado: null,
    nomeMes(m) {
        return ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'][m-1];
    },
    diasDisponiveis: [21,22,24,25,28,29],
    horarios: ['08:00','09:00','10:00','11:00','14:00','15:00','16:00','17:00'],
    ocupados: ['10:00','15:00'],
}">

    {{-- Steps --}}
    <div class="d-flex booking-steps pt-4">
        <div class="booking-step done flex-fill">
            <div class="booking-step__num"><i class="bi bi-check" style="font-size:0.875rem;"></i></div>
            <span class="booking-step__label">Serviço</span>
        </div>
        <div class="booking-step active flex-fill">
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

    <div class="row g-3 mb-5">
        {{-- Calendário --}}
        <div class="col-12 col-md-7">
            <div class="sabenta-card p-4">
                <div class="cal-nav">
                    <button class="btn btn-sm btn-outline-secondary" @click="mesSelecionado > 1 ? mesSelecionado-- : (mesSelecionado=12, anoSelecionado--)">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span class="cal-nav__month" x-text="nomeMes(mesSelecionado) + ' ' + anoSelecionado"></span>
                    <button class="btn btn-sm btn-outline-secondary" @click="mesSelecionado < 12 ? mesSelecionado++ : (mesSelecionado=1, anoSelecionado++)">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <div class="cal-grid mb-2">
                    <div class="cal-header">D</div><div class="cal-header">S</div><div class="cal-header">T</div>
                    <div class="cal-header">Q</div><div class="cal-header">Q</div><div class="cal-header">S</div>
                    <div class="cal-header">S</div>
                </div>
                <div class="cal-grid">
                    {{-- offset for April 2026 starting Wednesday (offset=3) --}}
                    <template x-for="i in 3"><div class="cal-day vazio"></div></template>
                    <template x-for="d in 30" :key="d">
                        <div class="cal-day"
                             :class="{
                                 disponivel: diasDisponiveis.includes(d),
                                 indisponivel: !diasDisponiveis.includes(d),
                                 selecionado: diaSelecionado === d,
                                 hoje: d === 20
                             }"
                             @click="diasDisponiveis.includes(d) && (diaSelecionado = d, horarioSelecionado = null)"
                             x-text="d">
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Horários --}}
        <div class="col-12 col-md-5">
            <div class="sabenta-card p-4 h-100">
                <template x-if="!diaSelecionado">
                    <div class="text-center py-4" style="color:var(--sabenta-text-muted);">
                        <i class="bi bi-calendar3" style="font-size:2rem;opacity:0.3;display:block;margin-bottom:0.75rem;"></i>
                        <span style="font-size:0.875rem;">Selecione uma data para ver os horários disponíveis</span>
                    </div>
                </template>
                <template x-if="diaSelecionado">
                    <div>
                        <p class="mb-3" style="font-family:var(--font-heading);font-weight:600;font-size:0.875rem;">
                            Horários disponíveis
                        </p>
                        <div class="d-grid gap-2" style="grid-template-columns:1fr 1fr;">
                            <template x-for="h in horarios" :key="h">
                                <div class="time-slot"
                                     :class="{ selected: horarioSelecionado === h, busy: ocupados.includes(h) }"
                                     @click="!ocupados.includes(h) && (horarioSelecionado = h)"
                                     x-text="h">
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Resumo + CTA --}}
    <template x-if="horarioSelecionado">
        <div class="sabenta-card p-4 mb-5" x-transition>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:0.8125rem;color:var(--sabenta-text-muted);">Horário selecionado</div>
                    <div style="font-family:var(--font-heading);font-weight:700;font-size:1rem;" x-text="diaSelecionado + '/04/2026 às ' + horarioSelecionado"></div>
                </div>
                <a href="{{ route('agendar.confirmacao', ['slug' => $slug ?? 'profissional']) }}"
                   class="btn btn-primary px-4">
                    Continuar <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </template>

</div>
@endsection
