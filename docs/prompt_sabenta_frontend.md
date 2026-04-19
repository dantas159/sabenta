# PROMPT — Geração completa do frontend do Sabenta

---

## Contexto do projeto

Você vai construir o frontend completo de um SaaS chamado **Sabenta** — um sistema de agendamento online para psicólogos e terapeutas. O projeto já tem o Laravel 12 instalado do zero (instalação limpa). Sua tarefa é criar **apenas o frontend**: views Blade, layouts, components, assets, rotas web estáticas e configuração do Vite. Nenhuma lógica de backend, nenhuma migration, nenhum model, nenhum controller real — apenas estrutura de rotas e views funcionais com dados fictícios onde necessário.

---

## Stack obrigatória

- **Laravel 12** (estrutura já criada)
- **Blade** para templates
- **Livewire 3** para componentes reativos (instale via `composer require livewire/livewire`)
- **Bootstrap 5.3** via npm
- **Bootstrap Icons** via npm
- **Alpine.js** (já vem com Livewire, mas importe separado também)
- **Vite** (já vem no Laravel 12 — apenas configure)
- **Google Fonts** para tipografia customizada

**Não usar:** Tailwind, Inertia, React, Vue.

---

## Identidade visual do Sabenta

### Logo
A logo do Sabenta é uma coruja com óculos (estilo profissional/inteligente) em tons de azul, ao lado do nome "Sabenta" em azul escuro (quase marinho). A identidade passa: **confiança, inteligência, acolhimento**.

### Paleta de cores (extraída da logo — use estas exatamente)
```css
:root {
  /* Cores principais */
  --sabenta-primary:     #1E5BAD;  /* Azul royal da coruja */
  --sabenta-primary-dark:#152D6E;  /* Azul marinho do texto da logo */
  --sabenta-primary-light:#4A8ED4; /* Azul claro das asas */
  --sabenta-accent:      #63B3F5;  /* Azul céu dos olhos */

  /* Neutros */
  --sabenta-bg:          #F4F7FC;  /* Fundo geral — azul acinzentado muito claro */
  --sabenta-surface:     #FFFFFF;  /* Cards e painéis */
  --sabenta-border:      #DDE4F0;  /* Bordas suaves */
  --sabenta-text:        #152D6E;  /* Texto principal — azul marinho */
  --sabenta-text-muted:  #6B7A99;  /* Texto secundário */

  /* Status */
  --status-confirmado:   #1D9E75;
  --status-pendente:     #D97706;
  --status-cancelado:    #6B7A99;
  --status-faltou:       #DC2626;
  --status-realizado:    #1E5BAD;
  --status-pago:         #1D9E75;
}
```

### Tipografia
```css
/* Importe do Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap');

--font-heading: 'Plus Jakarta Sans', sans-serif;  /* Títulos, sidebar, nav */
--font-body:    'DM Sans', sans-serif;             /* Corpo de texto, formulários */
```

### Personalidade do design
- **Clean e profissional**, mas com personalidade — não genérico
- Sidebar com fundo `--sabenta-primary-dark`, ícones e textos brancos
- Cards brancos com `border-radius: 12px` e sombra sutil `box-shadow: 0 2px 8px rgba(21,45,110,0.08)`
- Botão primário: fundo `--sabenta-primary`, hover escurece 10%
- Inputs com `border-radius: 8px`, borda `--sabenta-border`, focus com outline azul suave
- Tabelas sem bordas externas, apenas separadores internos suaves
- Badges arredondados (`border-radius: 20px`) com padding generoso

---

## Estrutura de arquivos a criar

```
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php          ← layout principal (sidebar + topbar + conteúdo)
│   │   ├── auth.blade.php         ← layout de autenticação (centralizado, sem sidebar)
│   │   └── public.blade.php       ← layout público (página do profissional + site)
│   ├── components/
│   │   ├── sidebar.blade.php      ← menu lateral
│   │   ├── topbar.blade.php       ← barra superior
│   │   ├── stat-card.blade.php    ← card de métrica (ícone + número + label)
│   │   ├── alert-card.blade.php   ← card de alerta colorido
│   │   ├── badge-status.blade.php ← badge colorido por status
│   │   ├── page-header.blade.php  ← título da página + breadcrumb + botão de ação
│   │   └── empty-state.blade.php  ← estado vazio (ícone + texto + botão)
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   ├── forgot-password.blade.php
│   │   └── reset-password.blade.php
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── agenda/
│   │   ├── dia.blade.php
│   │   ├── semana.blade.php
│   │   └── lista.blade.php
│   ├── pacientes/
│   │   ├── index.blade.php
│   │   ├── show.blade.php
│   │   └── notas.blade.php
│   ├── financeiro/
│   │   ├── index.blade.php
│   │   └── historico.blade.php
│   ├── automacoes/
│   │   └── index.blade.php
│   ├── configuracoes/
│   │   └── index.blade.php
│   ├── pagina-publica/
│   │   ├── editor.blade.php       ← editor dentro do painel
│   │   └── perfil.blade.php       ← página pública do profissional (sem sidebar)
│   ├── agendamento/               ← fluxo público do paciente
│   │   ├── inicio.blade.php       ← página do profissional com botão agendar
│   │   ├── horario.blade.php      ← seleção de data e hora
│   │   └── confirmacao.blade.php  ← dados do paciente + confirmação
│   └── site/                      ← site institucional do SaaS
│       ├── home.blade.php
│       ├── planos.blade.php
│       └── privacidade.blade.php
├── css/
│   └── app.css                    ← variáveis CSS + overrides Bootstrap + estilos Sabenta
└── js/
    └── app.js                     ← imports Bootstrap + Alpine + inicializações

public/
└── images/
    └── logo-sabenta.svg           ← crie um SVG inline da logo (coruja + texto Sabenta)
```

---

## Detalhamento de cada tela

### Layout `app.blade.php` (painel interno)

**Sidebar** (260px, fundo `--sabenta-primary-dark`):
- Topo: logo Sabenta (coruja + texto branco)
- Menu de navegação com ícones Bootstrap Icons:
  - `bi-speedometer2` → Painel
  - `bi-calendar3` → Agenda (com sub-itens: Dia / Semana / Lista — collapse)
  - `bi-people` → Pacientes
  - `bi-cash-coin` → Financeiro
  - `bi-chat-dots` → Automações
  - `bi-globe` → Minha Página
  - `bi-gear` → Configurações
- Rodapé da sidebar: avatar com iniciais + nome do profissional + link "Sair"
- Em mobile: offcanvas (botão hamburguer na topbar)

**Topbar** (fundo branco, sombra sutil):
- Lado esquerdo: botão hamburguer (mobile) + breadcrumb da página atual
- Lado direito: ícone de sino (notificações) + avatar do profissional com dropdown (Perfil / Configurações / Sair)

**Conteúdo**: `<main class="sabenta-main">` com padding 24px

**Footer interno**: versão `v1.0.0` + link "Suporte"

---

### Layout `auth.blade.php`

- Fundo dividido: lado esquerdo (40%) com fundo `--sabenta-primary-dark` e ilustração/texto de apresentação do produto. Lado direito (60%) branco com o formulário centralizado.
- Em mobile: apenas o lado direito (formulário), fundo `--sabenta-bg`
- Logo no topo do formulário
- Max-width do card: 420px

---

### Layout `public.blade.php`

- Navbar simples no topo: logo + link "Para profissionais" + botão "Criar conta"
- Conteúdo full-width
- Footer simples com copyright

---

### Tela: `auth/login.blade.php`
- Título: "Bem-vindo de volta"
- Subtítulo: "Entre na sua conta Sabenta"
- Campos: E-mail, Senha, checkbox "Lembrar de mim"
- Botão primário: "Entrar" (largura total, com wire:loading spinner)
- Link: "Esqueci minha senha" → rota `password.request`
- Link no rodapé: "Não tem conta? Criar conta grátis" → rota `register`
- Exibir `@error` abaixo de cada campo

---

### Tela: `auth/register.blade.php`
- Título: "Crie sua conta gratuita"
- Subtítulo: "7 dias grátis, sem cartão de crédito"
- Campos: Nome completo, E-mail, WhatsApp (máscara JS), Senha, Confirmar senha, checkbox Termos
- Botão: "Criar minha conta"
- Link: "Já tenho conta → Entrar"

---

### Tela: `auth/forgot-password.blade.php`
- Título: "Recuperar acesso"
- Campo: E-mail
- Botão: "Enviar link de recuperação"
- Link: "Voltar ao login"
- Mensagem de sucesso em `session('status')`

---

### Tela: `auth/reset-password.blade.php`
- Título: "Criar nova senha"
- Campos: Nova senha, Confirmar nova senha, input hidden token
- Botão: "Redefinir senha"

---

### Tela: `dashboard/index.blade.php`
- Usa `@extends('layouts.app')`, título da página "Painel"
- **Fila 1**: 4 stat-cards (dados fictícios com números reais para visual):
  - "Sessões hoje" → 6 → `bi-calendar-check` → azul
  - "Sessões esta semana" → 23 → `bi-calendar-week` → roxo
  - "Receita do mês" → R$ 4.200 → `bi-cash-coin` → verde
  - "Pacientes ativos" → 18 → `bi-people` → laranja
- **Fila 2 col-8**: Card "Sessões de hoje" com tabela de sessões fictícias (5 linhas com dados variados de status). Estado vazio quando não há sessões.
- **Fila 2 col-4**: Card "Alertas" com 3 alertas fictícios (1 vermelho faltou, 1 amarelo sem próxima sessão, 1 amarelo confirmação pendente)

---

### Tela: `agenda/dia.blade.php`
- Barra de controles: `< [Qui, 19 de Abril de 2025] >` + botão "Hoje" + botões "Dia" (ativo) / "Semana" / "Lista" + botão "Nova sessão" (primário)
- Grade de horários: 08:00 até 19:00, intervalos de 30min
- Coluna de tempo (60px) + coluna de sessões (resto)
- 3 sessões fictícias como blocos coloridos com nome do paciente e serviço
- 1 bloqueio fictício (cinza com `bi-lock`)
- **Modal 3-A** (Nova sessão): modal-lg com todos os campos especificados. Campos: Paciente (select), Serviço (select), Data (date), Horário (time), Duração (select: 30/45/50/60/90 min), Recorrência (select), Repetir até (date, oculto por padrão com Alpine x-show), Observações (textarea), Valor (number). Botões: "Salvar sessão" + "Cancelar". Modo editar adiciona botão "Excluir" danger.
- **Modal 3-B** (Detalhe sessão): modal com info da sessão + 4 botões de ação
- **Modal 3-C** (Bloquear horário): modal com campos de data/hora/motivo
- **Modal 3-D** (Excluir série): modal com 3 radio options

---

### Tela: `agenda/semana.blade.php`
- Grade 7 colunas (Seg–Dom) + coluna de horários
- Cabeçalho com dia abreviado + número
- Coluna do dia atual com fundo levemente azulado
- Sessões como blocos compactos
- Mesmos controles de navegação da visão dia

---

### Tela: `agenda/lista.blade.php`
- Filtros no topo: período (date range), paciente (select), status (select múltiplo), serviço (select)
- Tabela com 8 colunas especificadas. 10 linhas fictícias.
- Paginação Bootstrap
- Botão "Exportar CSV"

---

### Tela: `pacientes/index.blade.php`
- Busca inline (input com `bi-search`) + filtro status (select)
- Tabela: Nome | WhatsApp | Total sessões | Última sessão | Próxima sessão | Ações
- 8 pacientes fictícios com dados variados
- Botão "Novo paciente" abre **Modal 4-A**
- **Modal 4-A**: campos Nome, WhatsApp, E-mail, Data nascimento, Como chegou (select), Observações

---

### Tela: `pacientes/show.blade.php`
- Layout 2 colunas: col-4 ficha + col-8 conteúdo
- Ficha: avatar circular com iniciais, dados do paciente, 4 mini-stats (total sessões, realizadas, faltas, valor total)
- Abas (Bootstrap nav-tabs): "Histórico de sessões" | "Anotações privadas" | "Próximas sessões"
- Aba histórico: tabela de sessões do paciente
- Aba anotações: textarea grande com aviso de criptografia
- Aba próximas: lista simples com botão cancelar
- **Modal 4-C**: confirmação de exclusão com campo de digitação "EXCLUIR"

---

### Tela: `pacientes/notas.blade.php`
- Textarea grande (min-height: 400px)
- Indicador "Salvo" / "Salvando..." (Alpine)
- Data da última edição
- Aviso de privacidade em destaque

---

### Tela: `financeiro/index.blade.php`
- Filtro de período (select: Este mês / Mês anterior / Últimos 3 meses / Personalizado)
- 4 stat-cards: Receita realizada | A receber | Sessões realizadas | Ticket médio
- Gráfico de barras simples (use `<canvas>` com Chart.js CDN)
- Tabela de sessões do período com colunas: Data | Paciente | Serviço | Valor | Pagamento | Ações
- **Modal 5-A**: Registrar pagamento (forma, valor, data, observação)
- **Modal 5-B**: Enviar cobrança (preview da mensagem editável + botão Enviar)

---

### Tela: `financeiro/historico.blade.php`
- Filtros: período, paciente, forma de pagamento, status
- Tabela: Data sessão | Data pagamento | Paciente | Valor | Forma | Status
- Rodapé com total do período
- Botão "Exportar CSV"

---

### Tela: `automacoes/index.blade.php`
- Card de status da conexão WhatsApp (verde "Conectado" com número fictício / botão "Configurar")
- 6 cards de mensagens configuráveis, cada um com:
  - Nome da mensagem
  - Quando é disparada (texto muted)
  - Toggle on/off (Bootstrap form-switch)
  - Botão "Editar mensagem"
- **Modal 6-A**: Editar mensagem com textarea + preview ao vivo (Alpine x-model)
- Tabela de log de envios abaixo (10 linhas fictícias)

---

### Tela: `configuracoes/index.blade.php`
- Nav-tabs com 5 abas:
  1. **Horários**: tabela de dias da semana com campos início/fim/intervalo por linha. Toggle de ativo por dia.
  2. **Serviços**: tabela de serviços + modal novo/editar serviço
  3. **Cancelamentos**: select de janela mínima + textarea de política
  4. **Minha conta**: formulário de dados pessoais + foto de perfil + alteração de senha
  5. **Assinatura**: card do plano atual + tabela de faturas

---

### Tela: `pagina-publica/editor.blade.php`
- Layout 2 colunas: col-5 editor + col-7 preview
- Editor com seções em accordions: Status da página (toggle) | Aparência (paleta de cores) | Perfil (foto, nome, bio) | Especialidades (tags) | Seções visíveis (toggles)
- Preview: iframe ou div estilizado mostrando como ficará a página
- Botão "Salvar alterações" fixo no rodapé do editor
- Indicador "Alterações não salvas" (Alpine)

---

### Tela: `pagina-publica/perfil.blade.php` (pública, sem sidebar)
- Usa `layouts.public`
- Seções: Hero (foto + nome + especialidade + botão agendar) | Sobre | Especialidades (tags) | Tipos de sessão | Calendário disponível | Política de cancelamento
- Design atraente, voltado para o paciente
- Dados fictícios de uma "Dra. Ana Souza, Psicóloga Clínica"

---

### Telas de agendamento (fluxo do paciente)

**`agendamento/inicio.blade.php`** → igual ao `pagina-publica/perfil.blade.php` mas com barra de progresso no topo

**`agendamento/horario.blade.php`**:
- Barra de progresso: Passo 1 de 2
- Se houver mais de 1 serviço: cards de seleção de serviço
- Calendário mensal interativo (Alpine.js puro — dias disponíveis clicáveis, indisponíveis acinzentados)
- Ao selecionar dia: lista de horários disponíveis aparece ao lado/abaixo
- Botão "Próximo" habilitado apenas com data + hora selecionados

**`agendamento/confirmacao.blade.php`**:
- Barra de progresso: Passo 2 de 2
- Formulário: Nome, WhatsApp, E-mail (opcional), checkbox política
- Card de resumo ao lado: profissional, data, hora, serviço, duração, valor
- Botão "Confirmar agendamento"
- Tela de sucesso após confirmar (Alpine x-show)

---

### Site institucional

**`site/home.blade.php`**:
- Usa `layouts.public`
- Seções: Hero | Benefícios (3 cards) | Como funciona (3 passos) | CTA final
- Hero: headline "Sua agenda organizada. Seus pacientes sempre lembrados." + subtítulo + botão "Criar conta grátis" + mockup do sistema (pode ser div estilizado ou screenshot placeholder)
- Design atraente, convincente, com a paleta do Sabenta

**`site/planos.blade.php`**:
- Toggle Mensal/Anual (Alpine)
- 3 cards de planos: Solo (R$79), Clínica Pequena (R$199, destaque), Clínica Média (R$349)
- Tabela comparativa completa abaixo

**`site/privacidade.blade.php`**:
- Página de conteúdo com seções: Criptografia | Isolamento de dados | CFP | LGPD | Infraestrutura | FAQ

---

## Rotas (`routes/web.php`)

Crie todas as rotas como closures retornando `view(...)`. Organize em grupos com prefixo e nomes:

```php
// Site institucional
Route::get('/', fn() => view('site.home'))->name('home');
Route::get('/planos', fn() => view('site.planos'))->name('planos');
Route::get('/privacidade', fn() => view('site.privacidade'))->name('privacidade');

// Autenticação
Route::get('/entrar', fn() => view('auth.login'))->name('login');
Route::get('/criar-conta', fn() => view('auth.register'))->name('register');
Route::get('/recuperar-senha', fn() => view('auth.forgot-password'))->name('password.request');
Route::get('/redefinir-senha', fn() => view('auth.reset-password'))->name('password.reset');

// Painel (grupo com prefixo /painel)
Route::prefix('painel')->name('painel.')->group(function () {
    Route::get('/', fn() => view('dashboard.index'))->name('dashboard');
    Route::get('/agenda/dia', fn() => view('agenda.dia'))->name('agenda.dia');
    Route::get('/agenda/semana', fn() => view('agenda.semana'))->name('agenda.semana');
    Route::get('/agenda/lista', fn() => view('agenda.lista'))->name('agenda.lista');
    Route::get('/pacientes', fn() => view('pacientes.index'))->name('pacientes.index');
    Route::get('/pacientes/1', fn() => view('pacientes.show'))->name('pacientes.show');
    Route::get('/pacientes/1/notas', fn() => view('pacientes.notas'))->name('pacientes.notas');
    Route::get('/financeiro', fn() => view('financeiro.index'))->name('financeiro.index');
    Route::get('/financeiro/historico', fn() => view('financeiro.historico'))->name('financeiro.historico');
    Route::get('/automacoes', fn() => view('automacoes.index'))->name('automacoes.index');
    Route::get('/configuracoes', fn() => view('configuracoes.index'))->name('configuracoes.index');
    Route::get('/minha-pagina', fn() => view('pagina-publica.editor'))->name('pagina.editor');
});

// Agendamento público (paciente)
Route::prefix('agendar')->name('agendar.')->group(function () {
    Route::get('/{slug}', fn() => view('agendamento.inicio'))->name('inicio');
    Route::get('/{slug}/horario', fn() => view('agendamento.horario'))->name('horario');
    Route::get('/{slug}/confirmacao', fn() => view('agendamento.confirmacao'))->name('confirmacao');
});

// Página pública do profissional
Route::get('/p/{slug}', fn() => view('pagina-publica.perfil'))->name('perfil.publico');
```

---

## Configuração do Vite (`vite.config.js`)

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

---

## `resources/css/app.css` — estrutura esperada

```css
/* 1. Google Fonts import */
/* 2. Bootstrap import */
/* 3. Bootstrap Icons import */
/* 4. Variáveis CSS do Sabenta (:root) */
/* 5. Reset e base (body, html, fontes) */
/* 6. Layout: .sabenta-sidebar, .sabenta-topbar, .sabenta-main */
/* 7. Componentes: .stat-card, .alert-card, .sabenta-badge */
/* 8. Formulários: overrides de inputs, selects, textareas */
/* 9. Tabelas: overrides da table Bootstrap */
/* 10. Agenda: .agenda-grid, .agenda-slot, .agenda-event */
/* 11. Agendamento público: .booking-progress, .calendar-grid, .time-slots */
/* 12. Site institucional: .hero-section, .feature-card */
/* 13. Utilitários: .text-sabenta, .bg-sabenta, etc. */
```

---

## `resources/js/app.js` — estrutura esperada

```js
// Bootstrap JS
import 'bootstrap';

// Alpine.js (inicialização manual para controle)
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Máscaras de input (use IMask via npm: npm install imask)
// Inicializar máscara de WhatsApp nos inputs com data-mask="phone"

// Chart.js para gráficos (via CDN no blade, não aqui)

// Toast global do Livewire
// document.addEventListener('livewire:initialized', () => { ... })
```

---

## Componentes Blade reutilizáveis

### `components/stat-card.blade.php`
Props: `$titulo`, `$valor`, `$icone`, `$cor` (primary/success/warning/info), `$href`
```html
<div class="stat-card stat-card--{{ $cor }}">
  <div class="stat-card__icon"><i class="bi bi-{{ $icone }}"></i></div>
  <div class="stat-card__body">
    <div class="stat-card__value">{{ $valor }}</div>
    <div class="stat-card__label">{{ $titulo }}</div>
  </div>
</div>
```

### `components/page-header.blade.php`
Props: `$titulo`, `$breadcrumb` (array), `$botao` (opcional), `$botaoHref`, `$botaoIcone`

### `components/badge-status.blade.php`
Props: `$status` — mapeia para classe e texto correto

### `components/empty-state.blade.php`
Props: `$icone`, `$titulo`, `$descricao`, `$botao` (opcional), `$botaoHref`

### `components/alert-card.blade.php`
Props: `$tipo` (danger/warning/info), `$titulo`, `$descricao`, `$href`

---

## Instruções finais para execução

1. Crie todos os arquivos listados na estrutura
2. Todos os textos em **português brasileiro**
3. Use dados fictícios realistas (nomes brasileiros, valores em R$, datas recentes)
4. Todos os modais devem estar no mesmo arquivo blade da tela que os usa
5. A sidebar deve destacar o item ativo com fundo levemente mais claro e borda left branca
6. Todos os formulários devem ter `@csrf` e `method` corretos (mesmo sem processar)
7. Use `{{ route('nome.rota') }}` em todos os links e actions
8. O componente `<x-app-layout>` NÃO existe — use `@extends` e `@section` puro
9. Instale e configure as dependências npm: `npm install bootstrap @popperjs/core bootstrap-icons imask`
10. O arquivo `package.json` deve ter o script `"dev": "vite"` e `"build": "vite build"`
11. Garanta que `npm run build` funcione sem erros ao final
12. A logo SVG inline da coruja deve ser criada em `public/images/logo-sabenta.svg` — use um SVG simples mas reconhecível com coruja + texto "Sabenta"

---

## Ordem de criação recomendada

1. `app.css` (variáveis + base)
2. `app.js` (imports)
3. `vite.config.js`
4. `layouts/app.blade.php`
5. `layouts/auth.blade.php`
6. `layouts/public.blade.php`
7. Componentes (`stat-card`, `page-header`, `badge-status`, `empty-state`, `alert-card`)
8. Telas de autenticação (4 views)
9. Dashboard
10. Agenda (3 views + 4 modais)
11. Pacientes (3 views + modais)
12. Financeiro (2 views + modais)
13. Automações
14. Configurações
15. Editor da página pública
16. Página pública do profissional
17. Fluxo de agendamento (3 views)
18. Site institucional (3 views)
19. `routes/web.php`
20. Verificar `npm run build`
```
