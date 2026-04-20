@extends('layouts.public')

@section('title', 'Sabenta — Agenda online para psicólogos')

@push('styles')
<style>
    .hero {
        background: linear-gradient(155deg, #0f2460 0%, #1a3578 55%, #1e3d82 100%);
        padding: 5.5rem 0 7rem;
        position: relative;
        overflow: hidden;
        color: #fff;
    }
    .hero::before {
        content: '';
        position: absolute;
        top: -120px; right: -80px;
        width: 500px; height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(99,179,245,0.12) 0%, transparent 70%);
    }
    .hero::after {
        content: '';
        position: absolute;
        bottom: -1px; left: 0; right: 0;
        height: 80px;
        background: var(--sabenta-bg);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(99,179,245,0.15);
        border: 1px solid rgba(99,179,245,0.3);
        border-radius: 999px;
        padding: 0.375rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: rgba(255,255,255,0.85);
        margin-bottom: 1.75rem;
        letter-spacing: 0.02em;
    }
    .hero-headline {
        font-family: var(--font-heading);
        font-size: clamp(2rem, 5vw, 3.25rem);
        font-weight: 900;
        line-height: 1.12;
        margin-bottom: 1.25rem;
        letter-spacing: -0.025em;
    }
    .hero-headline span { color: var(--sabenta-accent); }
    .hero-sub {
        font-size: 1.0625rem;
        color: rgba(255,255,255,0.65);
        max-width: 480px;
        line-height: 1.7;
        margin-bottom: 2.25rem;
    }
    .hero-mockup {
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 20px;
        padding: 1.5rem;
        backdrop-filter: blur(8px);
    }
    .mock-topbar {
        display: flex; align-items: center; gap: 0.75rem;
        margin-bottom: 1.25rem;
    }
    .mock-dot { width:10px;height:10px;border-radius:50%; }
    .mock-session {
        background: rgba(255,255,255,0.07);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.625rem;
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }
    .mock-avatar {
        width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
        font-family: var(--font-heading);
    }
    .mock-badge {
        font-size: 0.65rem; font-weight: 700; border-radius: 6px;
        padding: 0.2rem 0.5rem; text-transform: uppercase;
    }

    .benefit-card {
        background: #fff;
        border-radius: 18px;
        padding: 2rem 1.75rem;
        box-shadow: 0 2px 24px rgba(15,36,96,0.07);
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .benefit-card:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(15,36,96,0.12); }
    .benefit-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.375rem;
        margin-bottom: 1.25rem;
    }

    .step-num {
        width: 44px; height: 44px; border-radius: 50%;
        background: var(--sabenta-primary-light);
        color: var(--sabenta-primary);
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1.25rem;
        flex-shrink: 0;
    }

    .testimonial-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.75rem;
        box-shadow: 0 2px 16px rgba(15,36,96,0.06);
    }
    .stars { color: #f59e0b; font-size: 0.875rem; letter-spacing: 0.1em; margin-bottom: 0.875rem; }

    .cta-section {
        background: linear-gradient(135deg, #1a3578 0%, #152D6E 100%);
        padding: 5rem 0;
        color: #fff;
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i>
                    Novidade — Lembretes por WhatsApp
                </div>
                <h1 class="hero-headline">
                    A agenda que<br>
                    <span>organiza sua clínica</span><br>
                    e fideliza pacientes
                </h1>
                <p class="hero-sub">
                    Agendamento online 24h, lembretes automáticos, gestão financeira e página de perfil profissional — tudo em um só lugar.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn btn-accent btn-lg px-4">
                        Começar grátis <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="{{ route('planos') }}" class="btn btn-lg px-4"
                       style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:#fff;">
                        Ver planos
                    </a>
                </div>
                <p style="font-size:0.78rem;color:rgba(255,255,255,0.45);margin-top:1rem;">
                    Grátis por 14 dias · Sem cartão de crédito · Cancele quando quiser
                </p>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-mockup">
                    <div class="mock-topbar">
                        <div class="mock-dot" style="background:#ff5f57;"></div>
                        <div class="mock-dot" style="background:#febc2e;"></div>
                        <div class="mock-dot" style="background:#28c840;"></div>
                        <span style="font-size:0.7rem;color:rgba(255,255,255,0.4);margin-left:0.5rem;">Sabenta · Agenda de hoje</span>
                    </div>
                    @foreach([
                        ['MC','#3b82f6','Mariana Costa','09:00 — 10:00','confirmado','#10b981'],
                        ['JP','#8b5cf6','João Pedro L.','10:00 — 11:00','pendente','#f59e0b'],
                        ['AS','#f43f5e','Ana Santos','14:00 — 15:00','confirmado','#10b981'],
                        ['RF','#0ea5e9','Roberto F.','16:00 — 17:00','confirmado','#10b981'],
                    ] as [$init,$cor,$name,$time,$status,$sc])
                    <div class="mock-session">
                        <div class="mock-avatar" style="background:{{ $cor }}22;color:{{ $cor }};">{{ $init }}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:0.8rem;font-weight:600;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $name }}</div>
                            <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);">{{ $time }}</div>
                        </div>
                        <div class="mock-badge" style="background:{{ $sc }}22;color:{{ $sc }};">{{ $status }}</div>
                    </div>
                    @endforeach
                    <div style="text-align:center;margin-top:1rem;font-size:0.7rem;color:rgba(255,255,255,0.3);">
                        4 sessões hoje · R$ 900,00 estimado
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Social proof strip --}}
<div style="background:#f8faff;border-top:1px solid var(--sabenta-border);border-bottom:1px solid var(--sabenta-border);padding:1.25rem 0;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-4 text-center">
            <div style="font-size:0.8125rem;color:var(--sabenta-text-muted);">
                <strong style="font-family:var(--font-heading);color:var(--sabenta-primary);font-size:1.25rem;">+800</strong>
                <span class="d-block">profissionais</span>
            </div>
            <div style="width:1px;height:32px;background:var(--sabenta-border);"></div>
            <div style="font-size:0.8125rem;color:var(--sabenta-text-muted);">
                <strong style="font-family:var(--font-heading);color:var(--sabenta-primary);font-size:1.25rem;">98%</strong>
                <span class="d-block">satisfação</span>
            </div>
            <div style="width:1px;height:32px;background:var(--sabenta-border);"></div>
            <div style="font-size:0.8125rem;color:var(--sabenta-text-muted);">
                <strong style="font-family:var(--font-heading);color:var(--sabenta-primary);font-size:1.25rem;">-40%</strong>
                <span class="d-block">faltas na agenda</span>
            </div>
            <div style="width:1px;height:32px;background:var(--sabenta-border);"></div>
            <div style="font-size:0.8125rem;color:var(--sabenta-text-muted);">
                <strong style="font-family:var(--font-heading);color:var(--sabenta-primary);font-size:1.25rem;">14 dias</strong>
                <span class="d-block">grátis</span>
            </div>
        </div>
    </div>
</div>

{{-- Benefícios --}}
<section style="padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family:var(--font-heading);font-size:2rem;font-weight:800;margin-bottom:0.75rem;">
                Tudo que você precisa para<br>organizar sua prática
            </h2>
            <p style="color:var(--sabenta-text-muted);max-width:500px;margin:0 auto;">
                Desenvolvido por e para profissionais de saúde mental, com foco em privacidade e praticidade.
            </p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-calendar-check','#3b82f6','#eff6ff','Agenda online 24/7','Seus pacientes agendam pelo link público. Você recebe notificação e confirma com um toque.'],
                ['bi-whatsapp','#10b981','#f0fdf4','Lembretes automáticos','Reduza faltas em até 40% com confirmações e lembretes via WhatsApp.'],
                ['bi-bar-chart-line','#8b5cf6','#faf5ff','Gestão financeira','Controle sessões pagas, pendentes e emita cobranças diretamente pelo WhatsApp.'],
                ['bi-shield-lock','#f59e0b','#fffbeb','Privacidade total','Dados criptografados, isolamento por clínica e conformidade com CFP e LGPD.'],
                ['bi-person-lines-fill','#0ea5e9','#f0f9ff','Prontuário digital','Anotações de sessão com controle de acesso e histórico completo por paciente.'],
                ['bi-globe','#f43f5e','#fff1f2','Página de perfil','Link público profissional para divulgar no Instagram, LinkedIn e Google.'],
            ] as [$icon,$cor,$bg,$titulo,$desc])
            <div class="col-md-6 col-lg-4">
                <div class="benefit-card">
                    <div class="benefit-icon" style="background:{{ $bg }};color:{{ $cor }};">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    <h5 style="font-family:var(--font-heading);font-weight:700;margin-bottom:0.5rem;">{{ $titulo }}</h5>
                    <p style="font-size:0.875rem;color:var(--sabenta-text-muted);line-height:1.65;margin:0;">{{ $desc }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Como funciona --}}
<section style="padding:5rem 0;background:#f8faff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family:var(--font-heading);font-size:2rem;font-weight:800;margin-bottom:0.75rem;">
                Como funciona
            </h2>
        </div>
        <div class="row g-4 justify-content-center">
            @foreach([
                ['1','Crie sua conta','Cadastre-se em 2 minutos, configure sua agenda e personalize sua página de perfil público.'],
                ['2','Compartilhe seu link','Divulgue seu link de agendamento no Instagram, WhatsApp ou e-mail. Seus pacientes agendam sozinhos.'],
                ['3','Atenda com tranquilidade','Receba confirmações automáticas, lembretes de sessão e controle tudo pelo painel Sabenta.'],
            ] as [$n,$t,$d])
            <div class="col-md-4">
                <div class="d-flex flex-column align-items-center text-center">
                    <div class="step-num">{{ $n }}</div>
                    <h5 style="font-family:var(--font-heading);font-weight:700;margin-bottom:0.5rem;">{{ $t }}</h5>
                    <p style="font-size:0.875rem;color:var(--sabenta-text-muted);max-width:260px;line-height:1.65;">{{ $d }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Depoimentos --}}
<section style="padding:5rem 0;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="font-family:var(--font-heading);font-size:2rem;font-weight:800;margin-bottom:0.5rem;">
                O que dizem nossos usuários
            </h2>
        </div>
        <div class="row g-4">
            @foreach([
                ['Fernanda Alves','Psicóloga · São Paulo','FA','#3b82f6','Reduzi as faltas em quase metade depois que comecei a usar os lembretes do Sabenta. Minha agenda está cheia e organizada.'],
                ['Carlos Mendes','Terapeuta · Rio de Janeiro','CM','#10b981','A parte financeira é incrível. Antes eu controlava tudo em planilha, agora vejo tudo no painel em segundos.'],
                ['Juliana Costa','Psicóloga · Belo Horizonte','JC','#8b5cf6','Meus pacientes adoram poder agendar pelo link. Recebi muitas mensagens dizendo que o processo é simples e moderno.'],
            ] as [$nome,$cargo,$init,$cor,$texto])
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="stars">★★★★★</div>
                    <p style="font-size:0.9rem;color:var(--sabenta-text);line-height:1.7;margin-bottom:1.25rem;">"{{ $texto }}"</p>
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:{{ $cor }}22;color:{{ $cor }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.875rem;font-family:var(--font-heading);">{{ $init }}</div>
                        <div>
                            <div style="font-weight:700;font-size:0.875rem;font-family:var(--font-heading);">{{ $nome }}</div>
                            <div style="font-size:0.775rem;color:var(--sabenta-text-muted);">{{ $cargo }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Final --}}
<section class="cta-section">
    <div class="container text-center">
        <h2 style="font-family:var(--font-heading);font-size:2.25rem;font-weight:900;margin-bottom:1rem;letter-spacing:-0.02em;">
            Pronto para organizar sua clínica?
        </h2>
        <p style="color:rgba(255,255,255,0.65);font-size:1rem;max-width:420px;margin:0 auto 2.25rem;">
            Comece grátis hoje. Sem cartão de crédito, sem burocracia.
        </p>
        <a href="{{ route('register') }}" class="btn btn-accent btn-lg px-5">
            Criar conta grátis <i class="bi bi-arrow-right ms-2"></i>
        </a>
        <p style="font-size:0.78rem;color:rgba(255,255,255,0.35);margin-top:1rem;">
            14 dias grátis · Cancele quando quiser
        </p>
    </div>
</section>

@endsection
