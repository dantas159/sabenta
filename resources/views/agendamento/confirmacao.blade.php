@extends('layouts.public')

@section('title', 'Confirmar agendamento')

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

    .summary-row { display:flex;justify-content:space-between;align-items:center;padding:0.625rem 0;border-bottom:1px solid var(--sabenta-border); }
    .summary-row:last-child { border-bottom:none; }
    .summary-label { font-size:0.8125rem;color:var(--sabenta-text-muted); }
    .summary-value { font-size:0.875rem;font-weight:600;color:var(--sabenta-text); }

    .success-wrapper {
        text-align:center;padding:3rem 2rem;
    }
    .success-icon {
        width:80px;height:80px;border-radius:50%;background:rgba(16,185,129,0.1);
        display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;
    }
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

<div class="container" style="max-width:660px;"
     x-data="{ confirmado: false, nome: '', telefone: '', email: '', loading: false }">

    {{-- Steps --}}
    <div class="d-flex booking-steps pt-4">
        <div class="booking-step done flex-fill">
            <div class="booking-step__num"><i class="bi bi-check" style="font-size:0.875rem;"></i></div>
            <span class="booking-step__label">Serviço</span>
        </div>
        <div class="booking-step done flex-fill">
            <div class="booking-step__num"><i class="bi bi-check" style="font-size:0.875rem;"></i></div>
            <span class="booking-step__label">Horário</span>
        </div>
        <div class="booking-step active flex-fill">
            <div class="booking-step__num">3</div>
            <span class="booking-step__label">Seus dados</span>
        </div>
        <div class="booking-step flex-fill" :class="{ done: confirmado }">
            <div class="booking-step__num">
                <template x-if="confirmado"><i class="bi bi-check" style="font-size:0.875rem;"></i></template>
                <template x-if="!confirmado"><span>4</span></template>
            </div>
            <span class="booking-step__label">Confirmação</span>
        </div>
    </div>

    {{-- Formulário --}}
    <template x-if="!confirmado">
        <div x-transition>
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-7">
                    <div class="sabenta-card p-4">
                        <h5 style="font-family:var(--font-heading);font-weight:700;margin-bottom:1.25rem;">Seus dados</h5>
                        <div class="mb-3">
                            <label class="form-label">Nome completo *</label>
                            <input type="text" class="form-control" x-model="nome" placeholder="Seu nome" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">WhatsApp *</label>
                            <input type="tel" class="form-control" x-model="telefone" data-mask="phone" placeholder="(11) 99999-9999" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control" x-model="email" placeholder="seu@email.com">
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="lgpdCheck" required>
                            <label class="form-check-label" for="lgpdCheck" style="font-size:0.8125rem;">
                                Concordo com o uso dos meus dados para fins de agendamento, em conformidade com a
                                <a href="{{ route('privacidade') }}" target="_blank">Política de Privacidade</a>.
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-5">
                    <div class="sabenta-card p-4" style="background:var(--sabenta-primary-light);border-color:var(--sabenta-primary-ultra-light, #dbeafe);">
                        <p style="font-family:var(--font-heading);font-weight:700;font-size:0.875rem;margin-bottom:1rem;">Resumo</p>
                        <div class="summary-row">
                            <span class="summary-label">Serviço</span>
                            <span class="summary-value">Atendimento Individual</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Data</span>
                            <span class="summary-value">21/04/2026</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Horário</span>
                            <span class="summary-value">09:00</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Duração</span>
                            <span class="summary-value">50 min</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Valor</span>
                            <span class="summary-value" style="color:var(--sabenta-primary);font-size:1rem;">R$ 200,00</span>
                        </div>
                        <div class="mt-3 p-3 rounded-3" style="background:rgba(99,179,245,0.12);font-size:0.78rem;color:var(--sabenta-text-muted);">
                            <i class="bi bi-info-circle me-1"></i>
                            O pagamento é combinado diretamente com o profissional.
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button class="btn btn-primary btn-lg px-5"
                        :disabled="!nome || !telefone || loading"
                        @click="loading = true; setTimeout(() => { confirmado = true; loading = false; }, 1200)">
                    <span x-show="!loading">Confirmar agendamento <i class="bi bi-check-circle ms-1"></i></span>
                    <span x-show="loading"><i class="bi bi-arrow-repeat spin me-1"></i>Confirmando…</span>
                </button>
            </div>
        </div>
    </template>

    {{-- Sucesso --}}
    <template x-if="confirmado">
        <div class="sabenta-card mb-5" x-transition>
            <div class="success-wrapper">
                <div class="success-icon">
                    <i class="bi bi-check-circle-fill" style="font-size:2.25rem;color:var(--sabenta-success);"></i>
                </div>
                <h2 style="font-family:var(--font-heading);font-weight:800;font-size:1.5rem;margin-bottom:0.5rem;">
                    Agendamento confirmado!
                </h2>
                <p style="color:var(--sabenta-text-muted);font-size:0.9rem;max-width:380px;margin:0 auto 2rem;">
                    Você receberá uma confirmação no seu WhatsApp. Em caso de dúvidas, entre em contato com o profissional.
                </p>

                <div class="row g-3 justify-content-center mb-3">
                    <div class="col-auto">
                        <div style="background:var(--sabenta-primary-light);border-radius:12px;padding:1rem 1.5rem;text-align:left;">
                            <div style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:0.125rem;">Data e hora</div>
                            <div style="font-weight:700;font-family:var(--font-heading);">21/04/2026 · 09:00</div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div style="background:var(--sabenta-primary-light);border-radius:12px;padding:1rem 1.5rem;text-align:left;">
                            <div style="font-size:0.75rem;color:var(--sabenta-text-muted);margin-bottom:0.125rem;">Serviço</div>
                            <div style="font-weight:700;font-family:var(--font-heading);">Atendimento Individual · 50 min</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="btn btn-outline-secondary mt-2">
                    Voltar ao início
                </a>
            </div>
        </div>
    </template>

</div>
@endsection
