@extends('layouts.public')
@section('title', 'Dra. Ana Souza — Agenda Online')

@push('styles')
<style>
.perfil-hero { background: linear-gradient(135deg, rgba(30,91,173,.05) 0%, rgba(99,179,245,.08) 100%); padding: 4rem 0 3rem; border-bottom: 1px solid var(--sabenta-border); }
.perfil-section { padding: 2.5rem 0; border-bottom: 1px solid var(--sabenta-border); }
.perfil-section:last-child { border-bottom: none; }
.servico-card { border: 1.5px solid var(--sabenta-border); border-radius: 12px; padding: 1.25rem; transition: border-color .2s, box-shadow .2s; }
.servico-card:hover { border-color: var(--sabenta-primary); box-shadow: 0 4px 16px rgba(30,91,173,.1); }
.cal-day { width: 38px; height: 38px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: .875rem; font-family: var(--font-heading); font-weight: 600; cursor: pointer; transition: background .15s, color .15s; }
.cal-day.disponivel { color: var(--sabenta-primary); }
.cal-day.disponivel:hover, .cal-day.selecionado { background: var(--sabenta-primary); color: #fff; }
.cal-day.indisponivel { color: var(--sabenta-border); cursor: not-allowed; }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="perfil-hero">
<div class="container">
<div class="row align-items-center g-4">
    <div class="col-md-8">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="sabenta-avatar" style="width:100px;height:100px;font-size:2rem;border-radius:24px;flex-shrink:0;box-shadow:0 8px 24px rgba(30,91,173,.2);">AS</div>
            <div>
                <h1 style="font-family:var(--font-heading);font-size:1.875rem;font-weight:800;color:var(--sabenta-text);margin-bottom:.35rem;">Dra. Ana Souza</h1>
                <p style="font-size:1rem;color:var(--sabenta-text-muted);margin-bottom:.75rem;">Psicóloga Clínica · <span style="color:var(--sabenta-primary);font-weight:600;">CRP 06/123456</span></p>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach(['Ansiedade','Depressão','TCC','Relacionamentos','Autoestima'] as $t)
                    <span style="background:rgba(30,91,173,.1);color:var(--sabenta-primary);padding:.3rem .875rem;border-radius:20px;font-size:.8rem;font-weight:600;">{{ $t }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="#agendar" class="btn btn-primary btn-lg">
            <i class="bi bi-calendar-check me-2"></i>Agendar consulta
        </a>
        <div style="font-size:.8rem;color:var(--sabenta-text-muted);margin-top:.75rem;">
            <i class="bi bi-shield-lock me-1"></i>Seus dados são protegidos
        </div>
    </div>
</div>
</div>
</section>

<div class="container">

{{-- Sobre --}}
<section class="perfil-section">
    <div class="row g-4">
        <div class="col-md-8">
            <h2 style="font-family:var(--font-heading);font-size:1.25rem;font-weight:700;margin-bottom:1rem;">Sobre mim</h2>
            <p style="font-size:.9375rem;line-height:1.8;color:var(--sabenta-text-muted);">
                Profissional com ampla experiência na área, atendo de forma presencial e online. Ofereço um serviço organizado, pontual e personalizado para cada cliente.
            </p>
            <p style="font-size:.9375rem;line-height:1.8;color:var(--sabenta-text-muted);">
                Meu compromisso é com a qualidade do atendimento e com a satisfação de quem me procura. Agende seu horário pelo link abaixo e escolha o dia e horário que melhor se encaixam na sua rotina.
            </p>
        </div>
        <div class="col-md-4">
            <div class="p-3 rounded-3" style="background:var(--sabenta-bg);border:1px solid var(--sabenta-border);">
                <div style="font-size:.8125rem;line-height:2.2;">
                    <div class="d-flex gap-2"><i class="bi bi-geo-alt" style="color:var(--sabenta-primary);"></i><span>São Paulo – SP (e online)</span></div>
                    <div class="d-flex gap-2"><i class="bi bi-clock" style="color:var(--sabenta-primary);"></i><span>Seg–Sex: 08h–18h · Sáb: 09h–12h</span></div>
                    <div class="d-flex gap-2"><i class="bi bi-translate" style="color:var(--sabenta-primary);"></i><span>Português</span></div>
                    <div class="d-flex gap-2"><i class="bi bi-camera-video" style="color:var(--sabenta-primary);"></i><span>Sessões presenciais e online</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Serviços --}}
<section class="perfil-section">
    <h2 style="font-family:var(--font-heading);font-size:1.25rem;font-weight:700;margin-bottom:1.25rem;">Tipos de sessão</h2>
    <div class="row g-3">
        @php $servicos = [
            ['Consulta Individual','50 min','R$ 200,00','Atendimento individual focado nos seus objetivos e necessidades.','person'],
            ['Consulta em Dupla','90 min','R$ 400,00','Atendimento para duas pessoas que buscam melhorar a comunicação e o relacionamento.','people'],
            ['Avaliação Inicial','60 min','R$ 350,00','Sessão de avaliação para entender suas necessidades e definir os próximos passos.','clipboard-check'],
        ]; @endphp
        @foreach($servicos as $s)
        <div class="col-md-4">
        <div class="servico-card">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(30,91,173,.1);display:flex;align-items:center;justify-content:center;font-size:1.125rem;color:var(--sabenta-primary);margin-bottom:.875rem;">
                <i class="bi bi-{{ $s[4] }}"></i>
            </div>
            <h3 style="font-family:var(--font-heading);font-size:.9375rem;font-weight:700;margin-bottom:.35rem;">{{ $s[0] }}</h3>
            <p style="font-size:.8rem;color:var(--sabenta-text-muted);line-height:1.55;margin-bottom:.875rem;">{{ $s[3] }}</p>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:.75rem;color:var(--sabenta-text-muted);">{{ $s[1] }}</div>
                    <div style="font-family:var(--font-heading);font-weight:700;font-size:1rem;color:var(--sabenta-text);">{{ $s[2] }}</div>
                </div>
                <a href="#agendar" class="btn btn-sm btn-primary">Agendar</a>
            </div>
        </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Calendário / agendar --}}
<section class="perfil-section" id="agendar">
    <h2 style="font-family:var(--font-heading);font-size:1.25rem;font-weight:700;margin-bottom:1.25rem;">Horários disponíveis</h2>
    <div class="row g-4">
        <div class="col-md-6" x-data="{ mes: 'Abril 2025', diaSelecionado: null }">
            <div style="border:1.5px solid var(--sabenta-border);border-radius:14px;padding:1.25rem;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-left"></i></button>
                    <span style="font-family:var(--font-heading);font-weight:700;font-size:.9375rem;" x-text="mes"></span>
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:.35rem;text-align:center;margin-bottom:.5rem;">
                    @foreach(['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $d)
                    <div style="font-size:.7rem;font-family:var(--font-heading);font-weight:700;color:var(--sabenta-text-muted);text-transform:uppercase;padding:.35rem 0;">{{ $d }}</div>
                    @endforeach
                </div>
                @php
                $disp = [7,9,10,14,15,16,17,21,22,23,24,28,29,30];
                $hoje = 17;
                @endphp
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:.35rem;text-align:center;">
                    @for($blank=0;$blank<2;$blank++)<div></div>@endfor
                    @for($d=1;$d<=30;$d++)
                    @if(in_array($d,$disp) && $d >= $hoje)
                    <div class="cal-day disponivel" @click="diaSelecionado = {{ $d }}"
                         :class="diaSelecionado === {{ $d }} ? 'selecionado' : ''">{{ $d }}</div>
                    @else
                    <div class="cal-day indisponivel">{{ $d }}</div>
                    @endif
                    @endfor
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div style="border:1.5px solid var(--sabenta-border);border-radius:14px;padding:1.25rem;">
                <h4 style="font-family:var(--font-heading);font-size:.9375rem;font-weight:700;margin-bottom:1rem;">Horários — Qui, 17/04</h4>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.5rem;">
                    @foreach(['08:00','09:00','10:00','11:00','14:00','14:30','15:00','16:00','17:00'] as $h)
                    <button class="btn btn-outline-secondary btn-sm" style="font-family:var(--font-heading);font-weight:600;font-size:.8375rem;">{{ $h }}</button>
                    @endforeach
                </div>
                <a href="{{ route('agendar.horario', 'ana-souza') }}" class="btn btn-primary w-100 mt-3">
                    <i class="bi bi-calendar-check me-1"></i>Confirmar horário
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Política --}}
<section class="perfil-section">
    <h2 style="font-family:var(--font-heading);font-size:1.25rem;font-weight:700;margin-bottom:1rem;">Política de cancelamento</h2>
    <div class="p-3 rounded-3" style="background:var(--sabenta-bg);border:1px solid var(--sabenta-border);font-size:.9rem;line-height:1.8;color:var(--sabenta-text-muted);">
        Cancelamentos devem ser realizados com no mínimo <strong>24 horas de antecedência</strong>. Cancelamentos tardios ou ausências sem aviso podem estar sujeitos à cobrança de 50% do valor da sessão.
        <br>Para reagendamentos, entre em contato por WhatsApp.
    </div>
</section>

</div>
@endsection
