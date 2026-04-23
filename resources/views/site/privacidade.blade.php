@extends('layouts.public')

@section('title', 'Política de Privacidade — Sabenta')

@push('styles')
<style>
    .privacy-hero {
        background: linear-gradient(155deg, #0f2460 0%, #1a3578 100%);
        padding: 3.5rem 0;
        color: #fff;
    }
    .privacy-nav {
        position: sticky;
        top: 68px;
        background: #fff;
        border-right: 1px solid var(--sabenta-border);
        padding: 1.25rem;
        height: fit-content;
        border-radius: 14px;
    }
    .privacy-nav a {
        display: block;
        font-size: 0.8375rem;
        color: var(--sabenta-text-muted);
        text-decoration: none;
        padding: 0.375rem 0.625rem;
        border-radius: 8px;
        margin-bottom: 0.125rem;
        transition: all 0.15s;
    }
    .privacy-nav a:hover, .privacy-nav a.active {
        background: var(--sabenta-primary-light);
        color: var(--sabenta-primary);
        font-weight: 600;
    }
    .privacy-section { margin-bottom: 3rem; scroll-margin-top: 90px; }
    .privacy-section h2 {
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 1rem;
        color: var(--sabenta-text);
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }
    .privacy-section h2 i {
        color: var(--sabenta-primary);
        font-size: 1.125rem;
    }
    .privacy-section p, .privacy-section li {
        font-size: 0.9rem;
        line-height: 1.75;
        color: var(--sabenta-text-muted);
    }
    .privacy-section ul { padding-left: 1.25rem; }
    .privacy-highlight {
        background: var(--sabenta-primary-light);
        border-left: 3px solid var(--sabenta-primary);
        border-radius: 0 10px 10px 0;
        padding: 1rem 1.25rem;
        margin: 1.25rem 0;
        font-size: 0.875rem;
        color: var(--sabenta-primary);
    }
    .faq-mini { border-bottom: 1px solid var(--sabenta-border); padding: 1rem 0; }
    .faq-mini:first-child { padding-top: 0; }
    .faq-mini:last-child { border-bottom: none; }
    .faq-mini strong { color: var(--sabenta-text); font-size: 0.875rem; }
    .faq-mini p { font-size: 0.8375rem; margin: 0.375rem 0 0; }
</style>
@endpush

@section('content')
<div class="privacy-hero">
    <div class="container">
        <p style="font-size:0.8rem;color:rgba(255,255,255,0.5);margin-bottom:0.5rem;">Atualizado em 01 de janeiro de 2026</p>
        <h1 style="font-family:var(--font-heading);font-size:2rem;font-weight:900;margin-bottom:0.5rem;">
            Política de Privacidade
        </h1>
        <p style="color:rgba(255,255,255,0.6);max-width:540px;font-size:0.9375rem;margin:0;">
            Sua privacidade é nossa prioridade. Esta política explica como coletamos, usamos e protegemos seus dados de acordo com a LGPD.
        </p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        {{-- Navegação lateral --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="privacy-nav">
                <div style="font-family:var(--font-heading);font-size:0.75rem;font-weight:700;color:var(--sabenta-text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:0.75rem;padding:0 0.625rem;">Seções</div>
                @foreach([
                    ['#criptografia','Criptografia'],
                    ['#isolamento','Isolamento de dados'],
                    ['#coleta','O que coletamos'],
                    ['#uso','Como usamos'],
                    ['#lgpd','Seus direitos (LGPD)'],
                    ['#infra','Infraestrutura'],
                    ['#faq','Perguntas frequentes'],
                    ['#contato','Contato'],
                ] as [$href,$label])
                <a href="{{ $href }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        {{-- Conteúdo --}}
        <div class="col-lg-9">

            <div class="privacy-section" id="criptografia">
                <h2><i class="bi bi-lock-fill"></i> Criptografia</h2>
                <p>Todos os dados transmitidos entre seu navegador e nossos servidores são protegidos com TLS 1.3. Os dados armazenados (informações de contato de clientes, dados de pagamento da assinatura) são criptografados em repouso.</p>
                <div class="privacy-highlight">
                    <i class="bi bi-shield-check me-2"></i>
                    Os dados de contato dos seus clientes são armazenados de forma segura — apenas você e sua equipe têm acesso.
                </div>
            </div>

            <div class="privacy-section" id="isolamento">
                <h2><i class="bi bi-diagram-3"></i> Isolamento de dados</h2>
                <p>Cada conta ou profissional opera em um ambiente de dados completamente isolado. Não existe compartilhamento de dados entre diferentes contas — nem para fins de análise interna.</p>
                <ul>
                    <li>Bancos de dados particionados por tenant</li>
                    <li>Logs de acesso auditáveis</li>
                </ul>
            </div>

            <div class="privacy-section" id="coleta">
                <h2><i class="bi bi-collection"></i> O que coletamos</h2>
                <p>Coletamos apenas o mínimo necessário para o funcionamento da plataforma:</p>
                <ul>
                    <li><strong>Dados do profissional:</strong> nome, e-mail, dados de pagamento da assinatura</li>
                    <li><strong>Dados dos clientes:</strong> nome, e-mail e telefone (inseridos pelo profissional para fins de agendamento)</li>
                    <li><strong>Dados de uso:</strong> logs de acesso, IP, navegador — usados exclusivamente para segurança</li>
                </ul>
                <p>Não vendemos, trocamos ou compartilhamos seus dados com terceiros para fins comerciais.</p>
            </div>

            <div class="privacy-section" id="uso">
                <h2><i class="bi bi-gear"></i> Como usamos seus dados</h2>
                <ul>
                    <li>Exibir e gerenciar agenda de atendimentos</li>
                    <li>Enviar lembretes e confirmações via WhatsApp (apenas com seu consentimento)</li>
                    <li>Processar pagamentos da assinatura</li>
                    <li>Gerar relatórios financeiros dentro da plataforma</li>
                    <li>Manter a segurança e integridade do sistema</li>
                </ul>
            </div>

            <div class="privacy-section" id="lgpd">
                <h2><i class="bi bi-person-lock"></i> Seus direitos (LGPD)</h2>
                <p>Em conformidade com a Lei nº 13.709/2018, você tem direito a:</p>
                <ul>
                    <li><strong>Confirmação e acesso</strong> — saber quais dados temos sobre você</li>
                    <li><strong>Correção</strong> — atualizar dados incompletos ou incorretos</li>
                    <li><strong>Exclusão</strong> — solicitar a exclusão de dados desnecessários</li>
                    <li><strong>Portabilidade</strong> — exportar seus dados em formato estruturado</li>
                    <li><strong>Revogação do consentimento</strong> — cancelar envio de comunicações a qualquer momento</li>
                </ul>
                <p>Para exercer qualquer desses direitos, entre em contato: <a href="mailto:privacidade@sabenta.com.br">privacidade@sabenta.com.br</a></p>
            </div>

            <div class="privacy-section" id="infra">
                <h2><i class="bi bi-server"></i> Infraestrutura</h2>
                <p>Todos os servidores do Sabenta estão localizados em território brasileiro, em conformidade com a LGPD. Utilizamos provedores de nuvem com certificações ISO 27001 e SOC 2 Type II.</p>
                <ul>
                    <li>Backups diários com retenção de 30 dias</li>
                    <li>Monitoramento de intrusão 24/7</li>
                    <li>Uptime garantido de 99,9%</li>
                </ul>
            </div>

            <div class="privacy-section" id="faq">
                <h2><i class="bi bi-question-circle"></i> Perguntas frequentes</h2>
                @foreach([
                    ['A equipe do Sabenta pode ver os dados dos meus clientes?','Não. Os dados de seus clientes ficam isolados na sua conta. Nossa equipe de suporte não acessa informações de clientes.'],
                    ['O que acontece com meus dados se eu cancelar?','Você pode exportar todos os dados antes de cancelar. Após o cancelamento, os dados são mantidos por 90 dias (para eventual reativação) e então excluídos permanentemente.'],
                    ['Os dados dos clientes são compartilhados?','Nunca. Os dados de seus clientes são de sua responsabilidade e não são acessados por outros profissionais ou pela equipe Sabenta.'],
                    ['Posso usar o Sabenta com múltiplos clientes simultâneos?','Sim. O sistema suporta múltiplos agendamentos e clientes. Cada pessoa consente com o uso de seus dados no momento do agendamento.'],
                ] as [$q,$r])
                <div class="faq-mini">
                    <strong>{{ $q }}</strong>
                    <p>{{ $r }}</p>
                </div>
                @endforeach
            </div>

            <div class="privacy-section" id="contato">
                <h2><i class="bi bi-envelope"></i> Contato do Encarregado (DPO)</h2>
                <p>Para dúvidas, solicitações ou denúncias relacionadas ao tratamento de dados pessoais:</p>
                <ul>
                    <li>E-mail: <a href="mailto:privacidade@sabenta.com.br">privacidade@sabenta.com.br</a></li>
                    <li>Prazo de resposta: até 15 dias úteis</li>
                    <li>Autoridade supervisora: <a href="https://www.gov.br/anpd" target="_blank" rel="noopener">ANPD — Autoridade Nacional de Proteção de Dados</a></li>
                </ul>
            </div>

        </div>
    </div>
</div>
@endsection
