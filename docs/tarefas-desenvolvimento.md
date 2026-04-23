# Tarefas de Desenvolvimento — Sabenta

**Versão:** 1.0  
**Data:** Abril de 2026  
**Stack:** Laravel 12 · Livewire 3 · MySQL 8.0 · Alpine.js · Bootstrap 5

---

## Como usar este documento

Cada tarefa segue o formato abaixo. **Leia tudo antes de começar a escrever código.**

```
ID        → Identificador único da tarefa (ex: T-001)
Título    → O que será feito
Objetivo  → Por que essa tarefa existe e qual problema ela resolve
Dependências → Quais tasks precisam estar concluídas antes
O que fazer  → Passo a passo do que implementar
Conceito-chave → O que você vai aprender ou precisar entender
Resultado esperado → Como saber que a task está pronta
```

> 📌 **Regra de ouro:** Nunca avance para a próxima fase sem as tasks da fase atual funcionando. O banco de dados é a fundação — se a fundação tiver erro, tudo que vem depois vai cair junto.

---

## Sumário de Fases

| Fase | Descrição | Tasks |
|---|---|---|
| 0 | Configuração e instalação | T-001 a T-005 |
| 1 | Migrações do banco de dados | T-010 a T-034 |
| 2 | PHP Enums | T-040 a T-056 |
| 3 | Models e relacionamentos | T-060 a T-082 |
| 4 | Autenticação | T-090 a T-096 |
| 5 | Middleware e autorização | T-100 a T-106 |
| 6 | Seeders e Factories | T-110 a T-117 |
| 7 | Dashboard | T-120 a T-123 |
| 8 | Módulo Agenda | T-130 a T-140 |
| 9 | Módulo Clientes | T-150 a T-154 |
| 10 | Módulo Financeiro | T-160 a T-165 |
| 11 | Módulo Automações | T-170 a T-175 |
| 12 | Módulo Configurações | T-180 a T-188 |
| 13 | Módulo Equipe | T-190 a T-195 |
| 14 | Página Pública e Agendamento Online | T-200 a T-210 |
| 15 | Jobs e Tarefas Agendadas | T-220 a T-228 |
| 16 | Observers e Auditoria | T-230 a T-232 |
| 17 | Testes | T-240 a T-248 |

---

---

# FASE 0 — Configuração e Instalação

> **Objetivo da fase:** Preparar o ambiente de desenvolvimento para que todos da equipe trabalhem com as mesmas ferramentas e configurações. Um ambiente mal configurado causa erros difíceis de diagnosticar.

---

## T-001 — Instalar o Livewire 3

**Objetivo:** O Livewire permite criar interfaces reativas (como modais, filtros em tempo real e atualizações sem recarregar a página) escrevendo PHP, sem precisar de JavaScript. Ele é o coração do frontend do Sabenta.

**Dependências:** Nenhuma.

**O que fazer:**

1. Abra o terminal na raiz do projeto e execute:
   ```bash
   composer require livewire/livewire
   ```
2. Publique os assets do Livewire:
   ```bash
   php artisan livewire:publish --config
   ```
3. Abra o arquivo `app/Http/Livewire/` — ele **não existe ainda**. O Livewire usa o caminho `app/Livewire/` por padrão no Laravel 12. Confirme que a pasta `app/Livewire/` já existe no projeto (ela foi criada antecipadamente).
4. Verifique se o layout `resources/views/layouts/app.blade.php` já possui as diretivas `@vite` no `<head>`. O Livewire 3 injeta seus scripts automaticamente — não é necessário adicionar nada manualmente ao layout.
5. No arquivo `config/livewire.php` (gerado pelo publish), localize a chave `'layout'` e confirme que aponta para `'layouts.app'`.

**Conceito-chave:** O Livewire funciona como um "componente PHP com estado". Cada componente tem uma classe PHP (que fica em `app/Livewire/`) e uma view Blade correspondente. Quando o usuário interage com a página, o Livewire faz uma requisição AJAX transparente, re-renderiza o componente e atualiza apenas o trecho da página que mudou.

**Resultado esperado:** `composer show livewire/livewire` exibe a versão instalada sem erros.

---

## T-002 — Configurar MySQL como banco de dados

**Objetivo:** O projeto usa SQLite por padrão (arquivo `database/database.sqlite`). A documentação define MySQL 8.0 como banco de produção. Precisamos configurar o ambiente para usar MySQL desde o início para evitar diferenças de comportamento entre desenvolvimento e produção.

**Dependências:** Nenhuma.

**O que fazer:**

1. Crie um banco de dados MySQL chamado `sabenta` (via MySQL Workbench, TablePlus, DBeaver ou linha de comando).
2. Abra o arquivo `.env` na raiz do projeto e altere as variáveis de banco:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sabenta
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```
3. Apague a linha `DB_CONNECTION=sqlite` se ela existir.
4. No arquivo `config/database.php`, confirme que o driver `mysql` já está configurado (ele vem por padrão no Laravel). Localize a chave `'charset'` dentro de `'mysql'` e defina:
   ```php
   'charset' => 'utf8mb4',
   'collation' => 'utf8mb4_unicode_ci',
   ```
   Isso garante suporte a emojis e caracteres especiais, conforme definido na documentação do banco.
5. Teste a conexão:
   ```bash
   php artisan db:show
   ```

**Conceito-chave:** O arquivo `.env` nunca deve ser commitado no Git (ele já está no `.gitignore`). Cada desenvolvedor da equipe tem seu próprio `.env` local. O arquivo `.env.example` serve como template compartilhado — sempre que você adicionar uma variável nova ao `.env`, adicione também ao `.env.example` sem o valor real.

**Resultado esperado:** O comando `php artisan db:show` exibe as informações do banco MySQL sem erro de conexão.

---

## T-003 — Configurar drivers de sessão, cache e fila

**Objetivo:** O Laravel usa `database` como driver para sessões, cache e filas neste projeto. Isso significa que essas informações ficam em tabelas do banco, não em arquivos ou memória. Precisamos configurar e renomear a tabela de sessões para evitar conflito com a tabela de domínio `sessoes`.

**Dependências:** T-002.

**O que fazer:**

1. Abra o `.env` e configure:
   ```
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database
   ```
2. Abra `config/session.php` e altere a chave `'table'`:
   ```php
   'table' => env('SESSION_TABLE', 'http_sessions'),
   ```
   > ⚠️ **Por que isso é importante:** A tabela padrão do Laravel se chamaria `sessions`. No Sabenta, `sessoes` é a tabela mais importante do sistema (agendamentos). Ter `sessions` e `sessoes` no mesmo banco causaria confusão constante em queries, logs e conversas de equipe. Renomeamos para `http_sessions` para deixar claro que é infraestrutura, não domínio.
3. A migration que cria essa tabela já existe em `database/migrations/0001_01_01_000000_create_users_table.php` ou similar. Vamos ajustá-la na Fase 1 (T-010).

**Resultado esperado:** As configurações estão salvas. Nenhuma migration rodada ainda — isso acontece na Fase 1.

---

## T-004 — Configurar o Vite e confirmar Alpine.js

**Objetivo:** O projeto já usa Alpine.js nas views (diretivas `x-data`, `x-model`, `x-show`). Precisamos garantir que o Alpine está instalado corretamente.

**Dependências:** Nenhuma.

**O que fazer:**

1. Verifique o `package.json`:
   ```bash
   cat package.json
   ```
2. Se `alpinejs` não aparecer nas dependências, instale:
   ```bash
   npm install alpinejs
   ```
3. Abra `resources/js/app.js` e adicione o Alpine se ele não estiver lá:
   ```js
   import Alpine from 'alpinejs'
   window.Alpine = Alpine
   Alpine.start()
   ```
4. Execute o build:
   ```bash
   npm run dev
   ```

**Resultado esperado:** `npm run dev` compila sem erros. As diretivas `x-data` e `x-show` funcionam nas views.

---

## T-005 — Criar estrutura de pastas do projeto

**Objetivo:** Antes de escrever código, é importante ter a estrutura de pastas organizada. O Laravel não cria automaticamente todas as pastas que vamos usar.

**Dependências:** Nenhuma.

**O que fazer:**

Crie as seguintes pastas (podem estar vazias — use um `.gitkeep` se necessário):

```
app/
├── Enums/
├── Livewire/
│   ├── Agendamento/
│   └── Painel/
│       ├── Agenda/
│       ├── Automacoes/
│       ├── Configuracoes/
│       ├── Equipe/
│       ├── Financeiro/
│       ├── Clientes/
│       └── PaginaPublica/
├── Models/
├── Services/
├── Jobs/
├── Observers/
├── Policies/
├── Http/
│   └── Middleware/
└── Traits/
```

**Conceito-chave:** No Laravel, a organização de pastas dentro de `app/` é livre — o framework não impõe uma estrutura rígida (exceto para `Models`, `Http` e `Providers`). A estrutura acima segue o padrão de organização por responsabilidade: cada pasta agrupa classes que fazem o mesmo tipo de trabalho.

**Resultado esperado:** Todas as pastas existem no projeto.

---

---

# FASE 1 — Migrações do Banco de Dados

> **Objetivo da fase:** Criar todas as tabelas do banco de dados na ordem correta, respeitando as dependências de chaves estrangeiras definidas na seção 19 da documentação do banco. **Cada migration deve ser criada e revisada antes de ser executada.** Execute `php artisan migrate` apenas ao final desta fase, depois de todas as migrations terem sido criadas e revisadas.

> 📌 **Conceito fundamental — O que é uma migration?** Uma migration é um arquivo PHP que descreve uma alteração no banco de dados. Ela funciona como um "histórico de versões" do seu banco. Com migrations, qualquer desenvolvedor da equipe pode recriar o banco inteiro rodando um único comando. Nunca altere o banco de dados manualmente — use sempre migrations.

> 📌 **Comando para criar uma migration:**
> ```bash
> php artisan make:migration create_nome_da_tabela_table
> ```
> O arquivo é criado em `database/migrations/` com um timestamp no nome.

---

## T-010 — Migration: `usuarios` (adaptar tabela users padrão)

**Objetivo:** O Laravel cria por padrão uma tabela `users` com campos em inglês. No Sabenta, todas as tabelas de domínio são em português. Precisamos adaptar a migration existente para refletir o schema definido na documentação (seção 5.1).

**Dependências:** T-002.

**O que fazer:**

1. Abra o arquivo `database/migrations/0001_01_01_000000_create_users_table.php`.
2. Substitua o conteúdo pelo schema da tabela `usuarios` conforme a documentação:
   - Renomeie a tabela para `usuarios`
   - Campos: `id`, `nome` (VARCHAR 150), `email` (VARCHAR 255, unique), `email_verificado_em` (nullable timestamp), `senha` (VARCHAR 255), `remember_token`, `criado_em`, `atualizado_em`, `excluido_em`
   - Use `$table->softDeletes('excluido_em')` para o soft delete
   - Use `$table->timestamps('criado_em', 'atualizado_em')` — **atenção:** no Laravel, o método `timestamps()` padrão cria `created_at` e `updated_at`. Para usar nomes em português, declare as colunas manualmente: `$table->timestamp('criado_em')->useCurrent()` e `$table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate()`
3. Na mesma migration, adapte a tabela de tokens de senha (`tokens_redefinicao_senha`) que fica no mesmo arquivo por padrão.
4. Adapte a tabela `http_sessions` (renomeada de `sessions`) — altere o campo `user_id` para referenciar `usuarios`.

**Conceito-chave:** O Laravel assume que a tabela de usuários se chama `users` e que os campos de timestamp são `created_at` e `updated_at`. Ao renomear para português, você precisará informar ao Eloquent esses novos nomes nos Models (Fase 3). Isso é feito via as propriedades `$table`, `CREATED_AT` e `UPDATED_AT` do model.

**Resultado esperado:** Migration criada e revisada. Nenhum `php artisan migrate` ainda.

---

## T-011 — Migration: `planos`

**Objetivo:** Criar a tabela que define os planos de assinatura disponíveis (Solo, Dupla, Equipe). Ela não depende de nenhuma outra tabela de domínio — por isso é criada cedo.

**Dependências:** T-010.

**O que fazer:**

1. Crie a migration:
   ```bash
   php artisan make:migration create_planos_table
   ```
2. Implemente o schema conforme a seção 6.1 da documentação:
   - `slug` VARCHAR(50) unique — identifica o plano em URLs
   - `preco_mensal` DECIMAL(10,2)
   - `preco_anual` DECIMAL(10,2) nullable
   - `limite_assentos` TINYINT UNSIGNED, default 1
   - `limite_clientes` INT UNSIGNED nullable (NULL = ilimitado)
   - `recursos` JSON — armazena feature flags (ver seção 17 da doc)
   - `ativo` boolean, default true
3. Adicione os índices documentados: `idx_planos_slug` (unique) e `idx_planos_ativos`.

**Conceito-chave:** O tipo `JSON` no MySQL 8.0 não é apenas um campo de texto — ele permite queries dentro do JSON usando operadores como `->` e `->>`. O Eloquent também consegue acessar chaves JSON automaticamente com casting. Vamos explorar isso na Fase 3.

**Resultado esperado:** Migration criada e revisada.

---

## T-012 — Migration: `profissionais`

**Objetivo:** Criar a tabela central do sistema — o tenant. Todo dado de domínio está vinculado a um `profissional_id`. Esta tabela tem relação 1:1 com `usuarios`.

**Dependências:** T-010.

**O que fazer:**

1. Crie a migration:
   ```bash
   php artisan make:migration create_profissionais_table
   ```
2. Implemente o schema conforme a seção 7.1 da documentação:
   - `usuario_id` BIGINT UNSIGNED unique — garante a relação 1:1 com `usuarios`
   - `nome_exibicao` VARCHAR(150)
   - `especialidade`, `registro_profissional`, `bio`, `foto_url` — todos nullable
   - `cor_agenda` VARCHAR(7) nullable — cor hexadecimal (ex: `#1E5BAD`)
   - `duracao_padrao_minutos` SMALLINT UNSIGNED, default 50
   - `valor_padrao` DECIMAL(10,2) nullable
   - `fuso_horario` VARCHAR(50), default `'America/Sao_Paulo'`
   - `ativo` boolean, default true
   - Soft delete com `excluido_em`
3. Defina a chave estrangeira `usuario_id → usuarios.id` com `onDelete('cascade')`.
4. Adicione os índices documentados.

**Conceito-chave:** A constraint `UNIQUE` na coluna `usuario_id` é o que garante a relação 1:1 no banco de dados. Sem ela, um usuário poderia ter múltiplos registros em `profissionais`. O Laravel cria constraints UNIQUE com `$table->unique('usuario_id')` ou usando `$table->foreignId('usuario_id')->unique()`.

**Resultado esperado:** Migration criada e revisada.

---

## T-013 — Migration: `assinaturas`

**Objetivo:** Registrar o contrato entre o profissional e o Sabenta. Uma assinatura ativa é pré-requisito para acesso ao painel.

**Dependências:** T-011, T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 6.2:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `plano_id` FK → `planos.id` (RESTRICT)
   - `status` ENUM: `trial`, `ativa`, `inadimplente`, `cancelada`, `expirada` — default `trial`
   - `periodo` ENUM: `mensal`, `anual` — default `mensal`
   - `trial_termina_em` DATE nullable
   - `inicio_em` DATE
   - `termina_em` DATE nullable
   - `cancelado_em` TIMESTAMP nullable
   - `gateway_id` VARCHAR(255) nullable
2. Adicione os índices: `(profissional_id, status)` e `(termina_em, status)`.

**Conceito-chave:** **ENUM no MySQL vs PHP Enum.** A coluna ENUM no banco restringe os valores aceitos no nível do banco de dados — se você tentar inserir um valor inválido, o MySQL rejeita. O PHP Enum (que criaremos na Fase 2) faz a mesma validação na camada da aplicação, antes mesmo de chegar ao banco. Usar os dois juntos é a abordagem mais segura.

**Resultado esperado:** Migration criada e revisada.

---

## T-014 — Migration: `faturas`

**Objetivo:** Registrar cada cobrança gerada pela assinatura. A tabela é imutável — nunca se edita uma fatura, apenas se cria novas.

**Dependências:** T-013.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 6.3:
   - `assinatura_id` FK → `assinaturas.id` (RESTRICT)
   - `profissional_id` FK → `profissionais.id` (RESTRICT) — desnormalizado para queries diretas
   - `valor` DECIMAL(10,2)
   - `status` ENUM: `aberta`, `paga`, `vencida`, `cancelada` — default `aberta`
   - `vencimento_em` DATE
   - `pago_em` TIMESTAMP nullable
   - `gateway_fatura_id` VARCHAR(255) nullable
   - `gateway_dados` JSON nullable
   - `criado_em` TIMESTAMP (sem `atualizado_em` — tabela imutável)
2. **Atenção:** NÃO use `$table->timestamps()`. Declare apenas `$table->timestamp('criado_em')->useCurrent()`.

**Conceito-chave:** **Tabela append-only.** Uma tabela append-only é aquela onde registros são apenas inseridos — nunca atualizados ou deletados manualmente. Isso garante rastreabilidade total. Se uma fatura mudar de status, o único campo que pode ser alterado é `status`. Qualquer outra alteração seria feita inserindo uma nova fatura. Isso é um padrão financeiro importante.

**Resultado esperado:** Migration criada e revisada.

---

## T-015 — Migration: `membros_equipe`

**Objetivo:** Registrar usuários adicionais que operam na conta de um profissional (secretaria, assistente, admin).

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 7.2:
   - `profissional_id` FK → `profissionais.id` (CASCADE)
   - `usuario_id` FK → `usuarios.id` (CASCADE)
   - `papel` ENUM: `secretaria`, `assistente`, `admin` — default `secretaria`
   - `ativo` boolean, default true
   - Soft delete com `excluido_em`
2. Crie constraint UNIQUE composta em `(profissional_id, usuario_id)` — evita que o mesmo usuário seja adicionado duas vezes à mesma conta.
3. Adicione os índices documentados.

**Resultado esperado:** Migration criada e revisada.

---

## T-016 — Migration: `convites`

**Objetivo:** Armazenar tokens de convite enviados por e-mail para adicionar membros à equipe.

**Dependências:** T-015.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 7.3:
   - `profissional_id` FK → `profissionais.id` (CASCADE)
   - `email` VARCHAR(255)
   - `papel` ENUM: `secretaria`, `assistente`, `admin`
   - `token` VARCHAR(100) unique
   - `aceito_em` TIMESTAMP nullable
   - `expira_em` TIMESTAMP
2. Adicione o índice `(expira_em, aceito_em)` para o job de limpeza.

**Resultado esperado:** Migration criada e revisada.

---

## T-017 — Migration: `servicos`

**Objetivo:** Tipos de atendimento oferecidos pelo profissional, com duração e valor.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.1:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `nome` VARCHAR(150)
   - `descricao` TEXT nullable
   - `duracao_minutos` SMALLINT UNSIGNED
   - `valor` DECIMAL(10,2)
   - `permite_agendamento_online` boolean, default true
   - `ativo` boolean, default true
   - Soft delete com `excluido_em`

**Resultado esperado:** Migration criada e revisada.

---

## T-018 — Migration: `salas`

**Objetivo:** Locais ou salas de atendimento do profissional. Opcional — usado quando atende em mais de um espaço.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.2:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `nome` VARCHAR(100)
   - `descricao` TEXT nullable
   - `ativo` boolean, default true

**Resultado esperado:** Migration criada e revisada.

---

## T-019 — Migration: `horarios_atendimento`

**Objetivo:** Definir a disponibilidade semanal do profissional. É a base para o cálculo de slots disponíveis.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.3:
   - `profissional_id` FK → `profissionais.id` (CASCADE)
   - `dia_semana` TINYINT UNSIGNED (0 = Domingo, 6 = Sábado)
   - `hora_inicio` TIME
   - `hora_fim` TIME
   - `intervalo_minutos` TINYINT UNSIGNED, default 60
   - `almoco_inicio` TIME nullable
   - `almoco_fim` TIME nullable
   - `ativo` boolean, default true
2. Adicione a constraint UNIQUE em `(profissional_id, dia_semana)` — apenas um horário por dia por profissional.
3. Adicione os CHECK constraints:
   ```php
   $table->rawIndex('(hora_fim > hora_inicio)', 'chk_horarios_ordem');
   // No MySQL 8.0, use DB::statement() na migration para adicionar CHECK:
   // DB::statement('ALTER TABLE horarios_atendimento ADD CONSTRAINT chk_horarios_ordem CHECK (hora_fim > hora_inicio)');
   ```

**Conceito-chave:** **CHECK constraints no Laravel.** O método `$table->check()` ainda não existe no Laravel 12 de forma nativa para todos os casos. A forma mais segura é usar `DB::statement()` dentro da migration para adicionar constraints CHECK diretamente via SQL. Sempre adicione a remoção da constraint no método `down()` da migration também.

**Resultado esperado:** Migration criada e revisada.

---

## T-020 — Migration: `bloqueios_agenda`

**Objetivo:** Períodos em que o profissional não está disponível (férias, feriados, compromissos pessoais).

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.4:
   - `profissional_id` FK → `profissionais.id` (CASCADE)
   - `titulo` VARCHAR(150) nullable
   - `inicio_em` DATETIME
   - `fim_em` DATETIME
   - `dia_inteiro` boolean, default false
   - Soft delete com `excluido_em`
2. Adicione CHECK: `fim_em > inicio_em`.
3. Adicione o índice `(profissional_id, inicio_em, fim_em, excluido_em)`.

**Resultado esperado:** Migration criada e revisada.

---

## T-021 — Migration: `politicas_cancelamento`

**Objetivo:** Regras de cancelamento do profissional — antecedência mínima e cobrança por ausência.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.5:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `nome` VARCHAR(150)
   - `horas_antecedencia` TINYINT UNSIGNED, default 24
   - `cobra_falta` boolean, default false
   - `percentual_cobranca` TINYINT UNSIGNED nullable
   - `ativo` boolean, default true
2. Adicione CHECK: `percentual_cobranca IS NULL OR percentual_cobranca BETWEEN 0 AND 100`.

**Resultado esperado:** Migration criada e revisada.

---

## T-022 — Migration: `clientes`

**Objetivo:** Cadastro operacional de clientes do profissional. Dados de contato apenas — sem informações pessoais sensíveis.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 8.1:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `nome` VARCHAR(150)
   - `whatsapp` VARCHAR(20) nullable — formato E.164
   - `email` VARCHAR(255) nullable
   - `data_nascimento` DATE nullable
   - `como_chegou` ENUM: `indicacao`, `google`, `redes_sociais`, `outro` — nullable
   - `observacoes` TEXT nullable
   - `ativo` boolean, default true
   - Soft delete com `excluido_em`
2. Adicione os índices, incluindo `idx_clientes_aniversario (profissional_id, data_nascimento)`.

**Resultado esperado:** Migration criada e revisada.

---

## T-023 — Migration: `consentimentos_cliente`

**Objetivo:** Registrar o aceite do cliente para uso de dados via LGPD. Obrigatório antes de enviar mensagens automáticas.

**Dependências:** T-022.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 8.2:
   - `cliente_id` FK → `clientes.id` (CASCADE)
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `tipo` ENUM: `termos_uso`, `receber_whatsapp`
   - `concedido` boolean
   - `criado_em` TIMESTAMP (sem `atualizado_em` — imutável)
2. Adicione constraint UNIQUE em `(cliente_id, tipo)`.

**Resultado esperado:** Migration criada e revisada.

---

## T-024 — Migration: `recorrencias`

**Objetivo:** Regra de repetição de atendimentos periódicos. Armazena a "instrução" — os atendimentos reais são gerados por um job.

**Dependências:** T-012, T-022, T-017, T-018, T-021.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.6:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `cliente_id` FK → `clientes.id` (RESTRICT)
   - `servico_id` FK → `servicos.id` (SET NULL) nullable
   - `sala_id` FK → `salas.id` (SET NULL) nullable
   - `politica_cancelamento_id` FK → `politicas_cancelamento.id` (SET NULL) nullable
   - `frequencia` ENUM: `semanal`, `quinzenal`, `mensal`
   - `dia_semana` TINYINT UNSIGNED nullable
   - `hora_inicio` TIME
   - `duracao_minutos` SMALLINT UNSIGNED
   - `valor` DECIMAL(10,2) nullable
   - `inicia_em` DATE
   - `termina_em` DATE nullable
   - `total_sessoes` SMALLINT UNSIGNED nullable
   - `ativo` boolean, default true
   - Soft delete com `excluido_em`
2. Adicione CHECK: `termina_em IS NULL OR termina_em >= inicia_em`.

**Resultado esperado:** Migration criada e revisada.

---

## T-025 — Migration: `sessoes` (+ trigger de sobreposição)

**Objetivo:** A tabela mais importante do sistema — cada agendamento confirmado. Esta migration também cria a trigger que impede double-booking.

**Dependências:** T-012, T-022, T-017, T-018, T-024, T-021.

**O que fazer:**

1. Crie a migration e implemente o schema da seção 9.7:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `cliente_id` FK → `clientes.id` (RESTRICT)
   - `servico_id` FK → `servicos.id` (SET NULL) nullable
   - `sala_id` FK → `salas.id` (SET NULL) nullable
   - `recorrencia_id` FK → `recorrencias.id` (SET NULL) nullable
   - `politica_cancelamento_id` FK → `politicas_cancelamento.id` (SET NULL) nullable
   - `inicio_em` DATETIME
   - `fim_em` DATETIME
   - `status` ENUM: `agendado`, `confirmado`, `realizado`, `faltou`, `cancelado` — default `agendado`
   - `status_pagamento` ENUM: `pendente`, `pago`, `isento`, `reembolsado` — default `pendente`
   - `valor` DECIMAL(10,2) nullable
   - `observacoes` TEXT nullable
   - `lembrete_enviado`, `confirmacao_enviada` boolean, default false
   - `cancelado_em` TIMESTAMP nullable
   - `motivo_cancelamento` TEXT nullable
   - `criado_por` FK → `usuarios.id` (SET NULL) nullable
   - Soft delete com `excluido_em`
2. Adicione todos os índices compostos documentados, especialmente `idx_sessoes_sobreposicao`.
3. Após criar a tabela, use `DB::unprepared()` para criar a trigger:
   ```php
   DB::unprepared("
       CREATE TRIGGER impedir_sobreposicao_sessao
       BEFORE INSERT ON sessoes
       FOR EACH ROW
       BEGIN
           IF EXISTS (
               SELECT 1 FROM sessoes
               WHERE profissional_id = NEW.profissional_id
               AND status != 'cancelado'
               AND excluido_em IS NULL
               AND inicio_em < NEW.fim_em
               AND fim_em > NEW.inicio_em
           ) THEN
               SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = 'Conflito de horário: já existe um atendimento neste período para este profissional';
           END IF;
       END
   ");
   ```
4. No método `down()` da migration, remova a trigger antes de dropar a tabela:
   ```php
   DB::unprepared('DROP TRIGGER IF EXISTS impedir_sobreposicao_sessao');
   ```

**Conceito-chave:** **Trigger de banco de dados.** Uma trigger é código SQL executado automaticamente pelo MySQL quando um INSERT ou UPDATE ocorre. Ela é a segunda camada de proteção contra double-booking — a primeira é o service `VerificadorDeDisponibilidade` (criado na Fase 8). A trigger garante que mesmo se dois usuários tentarem agendar no mesmo horário exatamente ao mesmo tempo (race condition), um deles receberá erro.

**Resultado esperado:** Migration criada e revisada.

---

## T-026 — Migration: `conexoes_whatsapp`

**Objetivo:** Armazenar a conexão ativa do profissional com o WhatsApp para disparo de mensagens automáticas.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration conforme seção 11.1:
   - `profissional_id` FK → `profissionais.id` (CASCADE)
   - `numero` VARCHAR(20) — formato E.164
   - `status` ENUM: `desconectado`, `conectando`, `conectado`, `bloqueado` — default `desconectado`
   - `ultimo_ping_em` TIMESTAMP nullable
   - `ativo` boolean, default true

**Resultado esperado:** Migration criada e revisada.

---

## T-027 — Migration: `automacoes`

**Objetivo:** Regras de mensagens automáticas configuradas pelo profissional — gatilho e template.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration conforme seção 11.2:
   - `profissional_id` FK → `profissionais.id` (CASCADE)
   - `nome` VARCHAR(150)
   - `tipo` ENUM: `confirmacao_agendamento`, `lembrete_48h`, `lembrete_dia`, `pos_sessao`, `cobranca`, `aniversario`
   - `mensagem` TEXT — template com variáveis `{nome}`, `{data}`, `{hora}`, `{servico}`
   - `gatilho_horas_antes` SMALLINT UNSIGNED nullable
   - `hora_disparo` TIME nullable — obrigatório para tipo `lembrete_dia`
   - `ativo` boolean, default true

**Resultado esperado:** Migration criada e revisada.

---

## T-028 — Migration: `logs_automacao`

**Objetivo:** Registro de cada tentativa de envio de mensagem automática. Evita re-envios duplicados.

**Dependências:** T-012, T-027, T-025, T-022.

**O que fazer:**

1. Crie a migration conforme seção 11.3:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `automacao_id` FK → `automacoes.id` (SET NULL) nullable
   - `sessao_id` FK → `sessoes.id` (SET NULL) nullable
   - `cliente_id` FK → `clientes.id` (SET NULL) nullable
   - `destinatario` VARCHAR(20) nullable
   - `mensagem_enviada` TEXT nullable
   - `status` ENUM: `pendente`, `enviado`, `falhou`, `lido` — default `pendente`
   - `erro` TEXT nullable
   - `enviado_em` TIMESTAMP nullable
   - `criado_em` TIMESTAMP (sem `atualizado_em`)
2. Adicione o índice `(sessao_id, automacao_id)` para verificação de duplicata.

**Resultado esperado:** Migration criada e revisada.

---

## T-029 — Migration: `transacoes`

**Objetivo:** Registro imutável de cada movimentação financeira. Estornos são novos registros, nunca edições.

**Dependências:** T-012, T-025, T-022, T-028.

> ⚠️ **Atenção à ordem:** `transacoes` agora depende de `logs_automacao` por causa do campo `log_automacao_id`. Confirme que a migration de `logs_automacao` (T-028) tem timestamp anterior a esta.

**O que fazer:**

1. Crie a migration conforme seção 10.1:
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `sessao_id` FK → `sessoes.id` (SET NULL) nullable
   - `cliente_id` FK → `clientes.id` (SET NULL) nullable
   - `tipo` ENUM: `recebimento`, `estorno`, `ajuste`
   - `valor` DECIMAL(10,2)
   - `forma_pagamento` ENUM: `dinheiro`, `pix`, `cartao_credito`, `cartao_debito`, `transferencia`, `outro`
   - `status` ENUM: `pendente`, `confirmado`, `cancelado` — default `pendente`
   - `referencia_externa` VARCHAR(255) nullable
   - `descricao` TEXT nullable
   - `origem_id` FK → `transacoes.id` (SET NULL) nullable — auto-referência para estornos
   - `log_automacao_id` FK → `logs_automacao.id` (SET NULL) nullable
   - `criado_por` FK → `usuarios.id` (SET NULL) nullable
   - `criado_em` TIMESTAMP (sem `atualizado_em` — tabela append-only)

**Resultado esperado:** Migration criada e revisada.

---

## T-030 — Migration: `paginas_publicas`

**Objetivo:** Página de perfil público do profissional, acessível via URL `/p/{slug}`.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration conforme seção 12.1:
   - `profissional_id` FK → `profissionais.id` (CASCADE) — UNIQUE (relação 1:1)
   - `slug` VARCHAR(100) unique
   - `titulo` VARCHAR(255) nullable
   - `bio_publica` TEXT nullable
   - `foto_url` VARCHAR(500) nullable
   - `secoes_visiveis` JSON nullable
   - `tags` JSON nullable
   - `publicada` boolean, default false

**Resultado esperado:** Migration criada e revisada.

---

## T-031 — Migration: `solicitacoes_agendamento`

**Objetivo:** Pedidos de agendamento feitos por clientes pela página pública. Ficam pendentes até aprovação.

**Dependências:** T-030, T-012, T-017, T-025.

**O que fazer:**

1. Crie a migration conforme seção 12.2:
   - `pagina_publica_id` FK → `paginas_publicas.id` (RESTRICT)
   - `profissional_id` FK → `profissionais.id` (RESTRICT)
   - `servico_id` FK → `servicos.id` (SET NULL) nullable
   - `nome` VARCHAR(150)
   - `whatsapp` VARCHAR(20)
   - `email` VARCHAR(255) nullable
   - `horario_solicitado` DATETIME
   - `mensagem` TEXT nullable
   - `status` ENUM: `pendente`, `aprovado`, `rejeitado`, `expirado` — default `pendente`
   - `expira_em` TIMESTAMP
   - `sessao_id` FK → `sessoes.id` (SET NULL) nullable
   - `ip_origem` VARCHAR(45) nullable
   - `lgpd_aceito` boolean, default false
   - `respondido_em` TIMESTAMP nullable

**Resultado esperado:** Migration criada e revisada.

---

## T-032 — Migration: `logs_auditoria`

**Objetivo:** Rastreabilidade de ações sensíveis. Fundamental para LGPD e investigação de incidentes.

**Dependências:** T-012.

**O que fazer:**

1. Crie a migration conforme seção 13.1:
   - `profissional_id` FK → `profissionais.id` (SET NULL) nullable
   - `usuario_id` FK → `usuarios.id` (SET NULL) nullable
   - `tipo_entidade` VARCHAR(100) — nome da tabela afetada
   - `id_entidade` BIGINT UNSIGNED
   - `acao` ENUM: `criou`, `atualizou`, `excluiu`, `restaurou`, `visualizou`, `login`, `login_falhou`
   - `dados_anteriores` JSON nullable
   - `dados_novos` JSON nullable
   - `ip` VARCHAR(45) nullable
   - `criado_em` TIMESTAMP (sem `atualizado_em` — imutável)

**Resultado esperado:** Migration criada e revisada.

---

## T-033 — Executar todas as migrations

**Objetivo:** Criar todas as tabelas no banco de dados MySQL.

**Dependências:** T-010 a T-032 criadas e revisadas.

**O que fazer:**

1. Antes de executar, revise a ordem dos timestamps nos nomes dos arquivos de migration. A ordem de execução do Laravel é determinada pelo timestamp no início do nome. Confirme que a sequência reflete as dependências da seção 19 da documentação.
2. Execute:
   ```bash
   php artisan migrate
   ```
3. Se houver erro, leia a mensagem com atenção. Erros de FK geralmente indicam que uma tabela referenciada ainda não existe — verifique a ordem dos timestamps.
4. Após sucesso, verifique as tabelas criadas:
   ```bash
   php artisan db:show --tables
   ```

**Resultado esperado:** Todas as 24 tabelas de domínio + tabelas de infraestrutura criadas sem erro. A trigger `impedir_sobreposicao_sessao` existe no banco (verifique via cliente MySQL).

---

---

# FASE 2 — PHP Enums

> **Objetivo da fase:** Criar enumerações PHP (disponíveis a partir do PHP 8.1) para representar os valores ENUM do banco de dados. Isso garante que o código da aplicação só use valores válidos e permite autocompletar na IDE.

> 📌 **Conceito fundamental — PHP Enum:** Um Enum é um tipo especial que define um conjunto fixo de valores possíveis. Em vez de usar strings soltas (`'ativa'`, `'cancelada'`) espalhadas pelo código — o que pode gerar typos — você usa `StatusAssinatura::ATIVA`, que a IDE valida em tempo de desenvolvimento.

> 📌 **Comando:** Os Enums ficam em `app/Enums/`. Crie os arquivos manualmente — o Laravel não tem um `artisan make:enum` por padrão no 12.

---

## T-040 — Enums de Assinatura e Fatura

**Objetivo:** Representar os status de assinatura e fatura como tipos seguros.

**Dependências:** T-033.

**O que fazer:**

Crie `app/Enums/StatusAssinatura.php`:
```php
<?php
namespace App\Enums;

enum StatusAssinatura: string
{
    case Trial       = 'trial';
    case Ativa       = 'ativa';
    case Inadimplente = 'inadimplente';
    case Cancelada   = 'cancelada';
    case Expirada    = 'expirada';
}
```

Crie `app/Enums/PeriodoAssinatura.php` com os valores `mensal` e `anual`.

Crie `app/Enums/StatusFatura.php` com os valores `aberta`, `paga`, `vencida`, `cancelada`.

**Conceito-chave:** Enums backed by string (`enum X: string`) têm um valor associado a cada case. Você pode obter o valor com `StatusAssinatura::Ativa->value` (retorna `'ativa'`) e criar a partir de uma string com `StatusAssinatura::from('ativa')`. O método `tryFrom()` retorna `null` em vez de lançar exceção se o valor não for encontrado.

**Resultado esperado:** Três arquivos de Enum criados em `app/Enums/`.

---

## T-041 — Enums de Equipe e Clientes

**Objetivo:** Representar papéis de membros da equipe e canais de origem de clientes.

**O que fazer:**

Crie `app/Enums/PapelMembroEquipe.php` com `secretaria`, `assistente`, `admin`.

Crie `app/Enums/ComoChegouCliente.php` com `indicacao`, `google`, `redes_sociais`, `outro`.

Crie `app/Enums/TipoConsentimento.php` com `termos_uso`, `receber_whatsapp`.

**Resultado esperado:** Três arquivos de Enum criados.

---

## T-042 — Enums de Agenda

**Objetivo:** Representar frequências de recorrência e status de atendimentos.

**O que fazer:**

Crie `app/Enums/FrequenciaRecorrencia.php` com `semanal`, `quinzenal`, `mensal`.

Crie `app/Enums/StatusSessao.php` com `agendado`, `confirmado`, `realizado`, `faltou`, `cancelado`.

Crie `app/Enums/StatusPagamentoSessao.php` com `pendente`, `pago`, `isento`, `reembolsado`.

**Resultado esperado:** Três arquivos de Enum criados.

---

## T-043 — Enums de Financeiro

**Objetivo:** Representar tipos de transação, formas de pagamento e status financeiro.

**O que fazer:**

Crie `app/Enums/TipoTransacao.php` com `recebimento`, `estorno`, `ajuste`.

Crie `app/Enums/FormaPagamento.php` com `dinheiro`, `pix`, `cartao_credito`, `cartao_debito`, `transferencia`, `outro`.

Crie `app/Enums/StatusTransacao.php` com `pendente`, `confirmado`, `cancelado`.

**Resultado esperado:** Três arquivos de Enum criados.

---

## T-044 — Enums de Automações e Auditoria

**Objetivo:** Representar tipos e status de automações, status de conexão WhatsApp e ações de auditoria.

**O que fazer:**

Crie `app/Enums/TipoAutomacao.php` com `confirmacao_agendamento`, `lembrete_48h`, `lembrete_dia`, `pos_sessao`, `cobranca`, `aniversario`.

Crie `app/Enums/StatusLogAutomacao.php` com `pendente`, `enviado`, `falhou`, `lido`.

Crie `app/Enums/StatusConexaoWhatsapp.php` com `desconectado`, `conectando`, `conectado`, `bloqueado`.

Crie `app/Enums/StatusSolicitacaoAgendamento.php` com `pendente`, `aprovado`, `rejeitado`, `expirado`.

Crie `app/Enums/AcaoAuditoria.php` com `criou`, `atualizou`, `excluiu`, `restaurou`, `visualizou`, `login`, `login_falhou`.

**Resultado esperado:** Cinco arquivos de Enum criados.

---

---

# FASE 3 — Models e Relacionamentos

> **Objetivo da fase:** Criar os Models Eloquent para cada tabela, definindo relacionamentos, casts, fillable e os atributos especiais do Sabenta (como nomes de colunas em português).

> 📌 **Conceito fundamental — Eloquent ORM:** O Eloquent é o ORM (Object-Relational Mapper) do Laravel. Ele mapeia cada linha do banco para um objeto PHP. Com ele, você escreve `Sessao::where('profissional_id', 1)->get()` em vez de SQL puro. Os relacionamentos (`hasOne`, `hasMany`, `belongsTo`) permitem navegar entre tabelas como se fossem propriedades do objeto.

> 📌 **Configurações importantes para este projeto:**
> ```php
> // Cada model precisa declarar:
> protected $table = 'nome_da_tabela';       // tabela em português
> const CREATED_AT = 'criado_em';             // timestamp em português
> const UPDATED_AT = 'atualizado_em';         // timestamp em português
> protected $dateFormat = 'Y-m-d H:i:s';     // formato de data
> ```

---

## T-060 — Model: `Usuario`

**Objetivo:** Adaptar o model `User` padrão do Laravel para o esquema em português do Sabenta.

**Dependências:** T-033, T-040 a T-044.

**O que fazer:**

1. Renomeie `app/Models/User.php` para `app/Models/Usuario.php`.
2. Atualize o namespace e a classe: `class Usuario extends Authenticatable`.
3. Configure as propriedades:
   ```php
   protected $table = 'usuarios';
   const CREATED_AT = 'criado_em';
   const UPDATED_AT = 'atualizado_em';
   ```
4. Atualize `$fillable` para os campos em português: `nome`, `email`, `senha`.
5. Atualize `$hidden` para: `senha`, `remember_token`.
6. No `casts()`, mapeie: `'email_verificado_em' => 'datetime'` e `'senha' => 'hashed'`.
7. Use `SoftDeletes` com: `use SoftDeletes; protected $dates = ['excluido_em'];`
8. Declare o relacionamento: `public function profissional(): HasOne`.
9. Atualize `config/auth.php` para apontar para `App\Models\Usuario`.
10. Atualize o `UserFactory` para usar os novos nomes de campos.

**Conceito-chave:** O Laravel usa o model `User` como padrão para autenticação, sessões, gates e notificações. Ao renomear, você precisa atualizar a configuração em `config/auth.php` na chave `'model'`. Se esquecer, o login vai quebrar.

**Resultado esperado:** Model `Usuario` funcional, sem erros no Tinker: `App\Models\Usuario::count()`.

---

## T-061 — Model: `Profissional`

**Objetivo:** Criar o model central do sistema — o tenant.

**Dependências:** T-060.

**O que fazer:**

1. Crie `app/Models/Profissional.php`.
2. Configure `$table`, timestamps e soft delete.
3. Defina relacionamentos:
   - `belongsTo(Usuario::class, 'usuario_id')`
   - `hasMany(Servico::class, 'profissional_id')`
   - `hasMany(Sala::class, 'profissional_id')`
   - `hasMany(Cliente::class, 'profissional_id')`
   - `hasMany(Sessao::class, 'profissional_id')`
   - `hasMany(Transacao::class, 'profissional_id')`
   - `hasMany(Automacao::class, 'profissional_id')`
   - `hasOne(PaginaPublica::class, 'profissional_id')`
   - `hasOne(Assinatura::class, 'profissional_id')` — a assinatura ativa

**Resultado esperado:** Model criado com todos os relacionamentos.

---

## T-062 a T-082 — Models restantes

**Objetivo:** Criar todos os Models restantes seguindo o mesmo padrão do `Profissional`.

**O que fazer para cada model:**

Para cada tabela listada abaixo, crie o model correspondente em `app/Models/`, configure `$table`, timestamps em português, soft delete quando aplicável, `$fillable`, `$casts` (incluindo os Enums criados na Fase 2) e todos os relacionamentos definidos na documentação do banco.

| Task | Model | Tabela | Relacionamentos principais |
|---|---|---|---|
| T-062 | `Plano` | `planos` | `hasMany(Assinatura)` |
| T-063 | `Assinatura` | `assinaturas` | `belongsTo(Profissional)`, `belongsTo(Plano)`, `hasMany(Fatura)` |
| T-064 | `Fatura` | `faturas` | `belongsTo(Assinatura)`, `belongsTo(Profissional)` |
| T-065 | `MembroEquipe` | `membros_equipe` | `belongsTo(Profissional)`, `belongsTo(Usuario)` |
| T-066 | `Convite` | `convites` | `belongsTo(Profissional)` |
| T-067 | `Servico` | `servicos` | `belongsTo(Profissional)`, `hasMany(Sessao)` |
| T-068 | `Sala` | `salas` | `belongsTo(Profissional)`, `hasMany(Sessao)` |
| T-069 | `HorarioAtendimento` | `horarios_atendimento` | `belongsTo(Profissional)` |
| T-070 | `BloqueioAgenda` | `bloqueios_agenda` | `belongsTo(Profissional)` |
| T-071 | `PoliticaCancelamento` | `politicas_cancelamento` | `belongsTo(Profissional)`, `hasMany(Sessao)`, `hasMany(Recorrencia)` |
| T-072 | `Cliente` | `clientes` | `belongsTo(Profissional)`, `hasMany(Sessao)`, `hasMany(Transacao)`, `hasOne(ConsentimentoCliente)` |
| T-073 | `ConsentimentoCliente` | `consentimentos_cliente` | `belongsTo(Cliente)`, `belongsTo(Profissional)` |
| T-074 | `Recorrencia` | `recorrencias` | `belongsTo(Profissional)`, `belongsTo(Cliente)`, `belongsTo(Servico)`, `hasMany(Sessao)` |
| T-075 | `Sessao` | `sessoes` | `belongsTo(Profissional)`, `belongsTo(Cliente)`, `belongsTo(Servico)`, `hasMany(Transacao)` |
| T-076 | `Transacao` | `transacoes` | `belongsTo(Profissional)`, `belongsTo(Sessao)`, `belongsTo(Cliente)` |
| T-077 | `ConexaoWhatsapp` | `conexoes_whatsapp` | `belongsTo(Profissional)` |
| T-078 | `Automacao` | `automacoes` | `belongsTo(Profissional)`, `hasMany(LogAutomacao)` |
| T-079 | `LogAutomacao` | `logs_automacao` | `belongsTo(Profissional)`, `belongsTo(Automacao)`, `belongsTo(Sessao)` |
| T-080 | `PaginaPublica` | `paginas_publicas` | `belongsTo(Profissional)`, `hasMany(SolicitacaoAgendamento)` |
| T-081 | `SolicitacaoAgendamento` | `solicitacoes_agendamento` | `belongsTo(PaginaPublica)`, `belongsTo(Profissional)`, `belongsTo(Sessao)` |
| T-082 | `LogAuditoria` | `logs_auditoria` | `belongsTo(Profissional)`, `belongsTo(Usuario)` |

**Conceito-chave — Casts com Enum:**
```php
protected function casts(): array
{
    return [
        'status' => StatusSessao::class,
        'status_pagamento' => StatusPagamentoSessao::class,
        'inicio_em' => 'datetime',
        'fim_em' => 'datetime',
    ];
}
```
Com esse cast, ao acessar `$sessao->status`, você recebe um objeto `StatusSessao` em vez de uma string. Isso permite usar `$sessao->status === StatusSessao::Realizado` em vez de `$sessao->status === 'realizado'`.

---

## T-083 — Global Scope de Multi-tenancy

**Objetivo:** Criar o mecanismo que garante que cada profissional veja apenas seus próprios dados. Este é o coração da arquitetura de tenancy do Sabenta.

**Dependências:** T-061.

**O que fazer:**

1. Crie `app/Scopes/ProfissionalScope.php`:
   ```php
   <?php
   namespace App\Scopes;

   use Illuminate\Database\Eloquent\Builder;
   use Illuminate\Database\Eloquent\Model;
   use Illuminate\Database\Eloquent\Scope;

   class ProfissionalScope implements Scope
   {
       public function apply(Builder $builder, Model $model): void
       {
           if (auth()->check() && auth()->user()->profissional) {
               $builder->where(
                   $model->getTable() . '.profissional_id',
                   auth()->user()->profissional->id
               );
           }
       }
   }
   ```

2. Crie a trait `app/Traits/BelongsToProfissional.php`:
   ```php
   <?php
   namespace App\Traits;

   use App\Scopes\ProfissionalScope;

   trait BelongsToProfissional
   {
       protected static function bootBelongsToProfissional(): void
       {
           static::addGlobalScope(new ProfissionalScope());
       }

       public static function semEscopo(): \Illuminate\Database\Eloquent\Builder
       {
           return static::withoutGlobalScope(ProfissionalScope::class);
       }
   }
   ```

3. Aplique a trait em todos os models de domínio (todos exceto `Usuario`, `Plano`, `Assinatura`, `Fatura`):
   ```php
   use BelongsToProfissional;
   ```

**Conceito-chave:** Um Global Scope é um filtro automático aplicado em todas as queries de um model. Com ele, `Sessao::all()` automaticamente adiciona `WHERE profissional_id = ?` na query, sem que o desenvolvedor precise lembrar de colocar esse filtro manualmente. É a proteção mais importante contra vazamento de dados entre profissionais.

**Resultado esperado:** `Sessao::toSql()` exibe `where profissional_id = ?` na query gerada.

---

---

# FASE 4 — Autenticação

> **Objetivo da fase:** Implementar o fluxo completo de autenticação: login, cadastro, recuperação de senha e verificação de e-mail. No Sabenta, o cadastro cria três registros em sequência: `usuario` → `profissional` → `assinatura` (trial).

---

## T-090 — Controllers de Autenticação

**Objetivo:** Criar os controllers responsáveis por processar os formulários de autenticação. As views já existem — agora precisamos de lógica de backend.

**Dependências:** T-083.

**O que fazer:**

1. Crie `app/Http/Controllers/Auth/LoginController.php`:
   - Método `store(Request $request)` que valida `email` e `senha`, autentica com `Auth::attempt(['email' => $email, 'senha' => $senha])` e redireciona para o painel
   - Método `destroy()` que chama `Auth::logout()` e redireciona para login

2. Crie `app/Http/Controllers/Auth/RegisterController.php`:
   - Método `store(Request $request)` que executa em transação:
     1. Cria o `Usuario`
     2. Cria o `Profissional` vinculado
     3. Busca o plano `Solo` (slug = 'solo')
     4. Cria a `Assinatura` com `status = trial` e `trial_termina_em = now()->addDays(14)`
   - Usa `DB::transaction()` para garantir que os três registros sejam criados ou nenhum seja

3. Crie `app/Http/Controllers/Auth/ForgotPasswordController.php` e `ResetPasswordController.php` seguindo a documentação do Laravel para reset de senha com tabela customizada `tokens_redefinicao_senha`.

4. Atualize `routes/web.php` para apontar as rotas de auth para esses controllers (substituindo os `fn() => view(...)` atuais).

**Conceito-chave:** `DB::transaction()` garante atomicidade — se qualquer operação dentro falhar, todas as anteriores são desfeitas automaticamente. Nunca crie registros relacionados em sequência sem uma transaction.

**Resultado esperado:** Fluxo completo de cadastro e login funcionando.

---

## T-091 — Autenticação com `senha` em vez de `password`

**Objetivo:** O Laravel assume que o campo de senha se chama `password`. Como usamos `senha`, precisamos informar isso ao sistema de autenticação.

**Dependências:** T-090.

**O que fazer:**

1. No model `Usuario`, sobrescreva o método `getAuthPassword()`:
   ```php
   public function getAuthPassword(): string
   {
       return $this->senha;
   }
   ```
2. No `config/auth.php`, confirme que `'provider'` aponta para `App\Models\Usuario` com `'password'` mapeado para `'senha'`. Se necessário, configure um `UserProvider` customizado.

**Resultado esperado:** `Auth::attempt(['email' => '...', 'senha' => '...'])` funciona corretamente.

---

---

# FASE 5 — Middleware e Autorização

> **Objetivo da fase:** Proteger as rotas do painel e garantir que apenas usuários autorizados acessem cada funcionalidade.

---

## T-100 — Middleware: AssinaturaAtiva

**Objetivo:** Bloquear acesso ao painel se a assinatura do profissional estiver expirada ou cancelada.

**Dependências:** T-091.

**O que fazer:**

1. Crie `app/Http/Middleware/AssinaturaAtiva.php`:
   - Verifica se `auth()->user()->profissional->assinaturas()->where('status', 'ativa')->orWhere('status', 'trial')->exists()`
   - Se não existir assinatura válida, redireciona para uma página de "assinatura expirada"
2. Registre o middleware em `bootstrap/app.php` com um alias: `'assinatura'`.
3. Aplique o middleware no grupo de rotas do painel em `routes/web.php`.

**Resultado esperado:** Acessar `/painel` sem assinatura ativa redireciona para página de aviso.

---

## T-101 — Middleware: ResolveProfissionalId

**Objetivo:** Quando um membro de equipe faz login, o sistema precisa resolver qual profissional ele está operando. Este middleware carrega esse contexto na sessão.

**Dependências:** T-091.

**O que fazer:**

1. Crie `app/Http/Middleware/ResolveProfissionalId.php`:
   - Verifica se o usuário autenticado é um profissional titular (`profissionais.usuario_id = auth()->id()`)
   - Se não for titular, busca o vínculo em `membros_equipe` (`usuario_id = auth()->id() AND ativo = 1`)
   - Coloca o `profissional_id` resolvido na sessão: `session(['profissional_id_ativo' => $id])`
2. Atualize o `ProfissionalScope` para usar `session('profissional_id_ativo')` em vez de `auth()->user()->profissional->id`.

**Resultado esperado:** Um membro de equipe que faz login vê os dados do profissional ao qual está vinculado.

---

## T-102 — Gates e Policies

**Objetivo:** Definir regras de autorização para as ações mais críticas do sistema.

**Dependências:** T-101.

**O que fazer:**

1. Crie `app/Policies/SessaoPolicy.php` com métodos:
   - `create(Usuario $user)` — secretaria e admin podem criar
   - `update(Usuario $user, Sessao $sessao)` — assistente só pode visualizar
   - `delete(Usuario $user, Sessao $sessao)` — apenas admin e secretaria
2. Crie `app/Policies/FinanceiroPolicy.php`:
   - `viewAny(Usuario $user)` — apenas profissional titular e admin
3. Registre as policies em `AppServiceProvider` usando `Gate::policy()`.
4. Crie um Gate para verificar feature flags do plano:
   ```php
   Gate::define('usar-automacoes', function (Usuario $user) {
       return $user->profissional->assinatura->plano->recursos['automacoes_whatsapp'] ?? false;
   });
   ```

**Resultado esperado:** `Gate::allows('usar-automacoes')` retorna false para plano Solo e true para Equipe.

---

---

# FASE 6 — Seeders e Factories

> **Objetivo da fase:** Criar dados iniciais obrigatórios (planos) e dados de teste (usuários, clientes, atendimentos) para que o desenvolvimento dos módulos seja feito com dados reais.

---

## T-110 — Seeder: Planos

**Objetivo:** Popular a tabela `planos` com os três planos do Sabenta. Esses dados são obrigatórios — o sistema não funciona sem eles.

**Dependências:** T-033.

**O que fazer:**

1. Crie `database/seeders/PlanosSeeder.php` com os três planos:

   | Nome | Slug | Preço mensal | Assentos | Clientes |
   |---|---|---|---|---|
   | Solo | solo | R$ 79,90 | 1 | 50 |
   | Dupla | dupla | R$ 129,90 | 2 | 200 |
   | Equipe | equipe | R$ 199,90 | 5 | NULL (ilimitado) |

2. O campo `recursos` de cada plano deve seguir o schema JSON da seção 17 da documentação.
3. Use `updateOrCreate(['slug' => ...], [...])` para que o seeder possa ser executado múltiplas vezes sem duplicar dados.
4. Chame o seeder no `DatabaseSeeder.php`.

**Resultado esperado:** `php artisan db:seed --class=PlanosSeeder` popula a tabela sem erros.

---

## T-111 — Factory: Usuario e Profissional

**Objetivo:** Criar factories para gerar dados de teste realistas.

**Dependências:** T-060, T-061.

**O que fazer:**

1. Adapte `database/factories/UserFactory.php` para o model `Usuario` com campos em português.
2. Crie `database/factories/ProfissionalFactory.php`.
3. Crie `database/factories/ClienteFactory.php` gerando nomes e WhatsApps brasileiros com o Faker:
   ```php
   'whatsapp' => '+5511' . fake()->numerify('#########'),
   'data_nascimento' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
   ```

**Resultado esperado:** `Usuario::factory()->create()` cria um usuário válido no banco.

---

## T-112 — Seeder: Dados de desenvolvimento

**Objetivo:** Criar um profissional de teste com clientes e atendimentos para usar durante o desenvolvimento.

**Dependências:** T-110, T-111.

**O que fazer:**

1. Crie `database/seeders/DevSeeder.php` que:
   - Cria um `Usuario` com email `dev@sabenta.com` e senha `password`
   - Cria um `Profissional` vinculado com nome "Profissional Teste"
   - Cria a `Assinatura` trial no plano Equipe
   - Cria 20 `Clientes` usando a factory
   - Cria `HorarioAtendimento` para segunda a sexta (08:00 às 18:00)
   - Cria 30 atendimentos (modelo `Sessao`) distribuídos entre os clientes nas últimas 4 semanas

**Resultado esperado:** `php artisan db:seed --class=DevSeeder` e login com `dev@sabenta.com` mostra um painel com dados reais.

---

---

# FASE 7 — Dashboard

> **Objetivo da fase:** Conectar a view do dashboard (`resources/views/dashboard/index.blade.php`) com dados reais do banco, substituindo os arrays hardcoded por queries Eloquent via Livewire.

---

## T-120 — Livewire: DashboardStats

**Objetivo:** Carregar os 4 stat cards do dashboard (atendimentos hoje, atendimentos semana, receita do mês, clientes ativos) com dados reais.

**Dependências:** T-112.

**O que fazer:**

1. Crie o componente:
   ```bash
   php artisan make:livewire Painel/DashboardStats
   ```
2. Na classe `app/Livewire/Painel/DashboardStats.php`, declare as propriedades e carregue no `mount()`:
   ```php
   public int $sessoesHoje;
   public int $sessoesSemana;
   public float $receitaMes;
   public int $clientesAtivos;

   public function mount(): void
   {
       $hoje = today();
       $this->sessoesHoje = Sessao::whereDate('inicio_em', $hoje)
           ->whereNotIn('status', [StatusSessao::Cancelado])
           ->count();
       // ... demais queries
   }
   ```
3. Substitua os valores hardcoded na view do dashboard por `<livewire:painel.dashboard-stats />`.

**Conceito-chave:** O método `mount()` do Livewire é executado uma vez quando o componente é carregado pela primeira vez — equivalente ao `__construct()`. Para dados que precisam atualizar sem reload de página, use `wire:poll` ou eventos Livewire.

**Resultado esperado:** O dashboard exibe contagens reais do banco.

---

## T-121 — Livewire: AtendimentosHoje

**Objetivo:** Listar os atendimentos do dia atual com dados reais, com capacidade de atualizar o status de cada atendimento.

**Dependências:** T-120.

**O que fazer:**

1. Crie `app/Livewire/Painel/AtendimentosHoje.php`.
2. Carregue os atendimentos do dia com `eager loading` dos relacionamentos:
   ```php
   Sessao::with(['cliente', 'servico'])
       ->whereDate('inicio_em', today())
       ->orderBy('inicio_em')
       ->get();
   ```
3. Implemente o método `atualizarStatus(int $sessaoId, string $novoStatus)` que valida o status com o Enum e salva.
4. Atualize a view correspondente do componente Livewire.

**Resultado esperado:** Lista de atendimentos do dia com dados reais e botão funcional de atualização de status.

---

## T-122 — Livewire: AlertasDashboard

**Objetivo:** Exibir alertas contextuais: atendimentos com falta, clientes sem próximo atendimento, confirmações pendentes.

**Dependências:** T-121.

**O que fazer:**

1. Crie `app/Livewire/Painel/AlertasDashboard.php` com as queries:
   - Atendimentos com `status = 'faltou'` nos últimos 7 dias
   - Clientes com o último atendimento há mais de 14 dias sem futuro agendado
   - Atendimentos dos próximos 2 dias com `status = 'agendado'` (não confirmado)
2. Limite cada categoria a 3 alertas no máximo para não sobrecarregar o dashboard.

**Resultado esperado:** Painel de alertas exibe situações reais dos dados de teste.

---

---

# FASE 8 — Módulo Agenda

> **Objetivo da fase:** Implementar as três views de agenda (dia, semana, lista) com funcionalidade completa de criação, edição, visualização e cancelamento de atendimentos.

---

## T-130 — Service: VerificadorDeDisponibilidade

**Objetivo:** Criar o serviço que verifica se um horário está disponível antes de criar um atendimento. Esta é a primeira camada de proteção contra double-booking.

**Dependências:** T-075.

**O que fazer:**

1. Crie `app/Services/VerificadorDeDisponibilidade.php`:
   ```php
   class VerificadorDeDisponibilidade
   {
       public function verificar(
           int $profissionalId,
           Carbon $inicio,
           Carbon $fim,
           ?int $sessaoIdIgnorar = null
       ): bool {
           return !Sessao::semEscopo()
               ->where('profissional_id', $profissionalId)
               ->where('status', '!=', StatusSessao::Cancelado->value)
               ->whereNull('excluido_em')
               ->where('inicio_em', '<', $fim)
               ->where('fim_em', '>', $inicio)
               ->when($sessaoIdIgnorar, fn($q) => $q->where('id', '!=', $sessaoIdIgnorar))
               ->exists();
       }
   }
   ```
2. Registre como singleton no `AppServiceProvider`:
   ```php
   $this->app->singleton(VerificadorDeDisponibilidade::class);
   ```

**Conceito-chave:** O método `semEscopo()` que criamos na Trait `BelongsToProfissional` remove o Global Scope para que este serviço possa verificar conflitos passando o `profissionalId` explicitamente — necessário porque o serviço pode ser chamado de contextos sem usuário autenticado (jobs, por exemplo).

**Resultado esperado:** `VerificadorDeDisponibilidade::verificar()` retorna `false` quando há sobreposição.

---

## T-131 — Service: SlotCalculator

**Objetivo:** Calcular os horários disponíveis de um profissional em um dia específico, cruzando horários de atendimento, atendimentos existentes e bloqueios.

**Dependências:** T-130.

**O que fazer:**

1. Crie `app/Services/SlotCalculator.php`:
   - Método `calcularSlotsDisponiveis(int $profissionalId, Carbon $data, int $duracaoMinutos): array`
   - Lógica:
     1. Busca o `HorarioAtendimento` do dia da semana correspondente
     2. Gera todos os slots possíveis entre `hora_inicio` e `hora_fim`, espaçados por `duracao + intervalo_minutos`
     3. Remove slots que colidem com o período de almoço (`almoco_inicio`/`almoco_fim`)
     4. Remove slots que colidem com atendimentos existentes (usa `VerificadorDeDisponibilidade`)
     5. Remove slots que colidem com bloqueios de agenda
     6. Retorna array de `Carbon` representando os slots disponíveis

**Resultado esperado:** `SlotCalculator::calcularSlotsDisponiveis()` retorna lista correta de horários.

---

## T-132 — Livewire: AgendaDia

**Objetivo:** Implementar a view de agenda do dia com dados reais, modal de criação e modal de detalhes.

**Dependências:** T-131.

**O que fazer:**

1. Crie `app/Livewire/Painel/Agenda/AgendaDia.php`:
   - Propriedade `public Carbon $data` — inicializada com hoje
   - Carrega sessões do dia com `with(['cliente', 'servico', 'sala'])`
   - Métodos `proximoDia()` e `diaAnterior()` que alteram `$data` e re-renderizam

2. Crie o subcomponente Livewire `NovoAtendimentoModal`:
   - Formulário com: cliente (busca por nome), serviço, data, horário (lista de slots calculados pelo `SlotCalculator`), observações
   - Validação com `validate()` do Livewire
   - Ao salvar: verifica disponibilidade → cria atendimento → fecha modal → emite evento para atualizar a lista

3. Crie o subcomponente `DetalhesAtendimentoModal`:
   - Exibe dados do atendimento
   - Botões de ação: Confirmar presença, Marcar como realizado, Registrar falta, Cancelar

4. Atualize `routes/web.php` para a rota do painel usar `AgendaDia` em vez de `fn() => view(...)`.

**Conceito-chave — Comunicação entre componentes Livewire:** Quando `NovoAtendimentoModal` salva um atendimento, ele precisa informar `AgendaDia` para atualizar a lista. Isso é feito com eventos: `$this->dispatch('atendimento-criado')` no modal e `#[On('atendimento-criado')]` no componente pai.

**Resultado esperado:** Agenda do dia funcional com navegação entre dias, criação e atualização de atendimentos.

---

## T-133 — Livewire: AgendaSemana e AgendaLista

**Objetivo:** Implementar as outras duas views de agenda.

**Dependências:** T-132.

**O que fazer:**

1. Crie `AgendaSemana.php`: exibe 7 colunas (um por dia da semana), carregando os atendimentos de cada dia em uma única query com `whereBetween('inicio_em', [$inicioDaSemana, $fimDaSemana])`.

2. Crie `AgendaLista.php`: exibe atendimentos em formato tabular com filtros por período, status e cliente. Implemente busca em tempo real com `wire:model.live`.

**Resultado esperado:** Três views de agenda funcionais e navegáveis.

---

## T-134 — Livewire: BloquearAgendaModal

**Objetivo:** Permitir que o profissional bloqueie períodos na sua agenda.

**Dependências:** T-132.

**O que fazer:**

Crie o componente com formulário para: título (opcional), data/hora de início, data/hora de fim, opção "dia inteiro". Ao salvar, cria registro em `bloqueios_agenda` e emite evento para atualizar a agenda.

**Resultado esperado:** Modal de bloqueio funcional integrado às três views de agenda.

---

---

# FASE 9 — Módulo Clientes

---

## T-150 — Livewire: ListaClientes

**Objetivo:** Listar clientes com busca em tempo real e filtros.

**Dependências:** T-072, T-112.

**O que fazer:**

1. Crie `app/Livewire/Painel/Clientes/ListaClientes.php`:
   - Propriedade `public string $busca = ''`
   - Query com `when($this->busca, fn($q) => $q->where('nome', 'like', "%{$this->busca}%"))` — o `wire:model.live` no input atualiza automaticamente
   - Paginação com `paginate(20)` do Eloquent

2. Conecte a view `resources/views/pacientes/index.blade.php` a este componente.

**Resultado esperado:** Busca de clientes funcional em tempo real.

---

## T-151 — Livewire: PerfilCliente

**Objetivo:** Exibir o perfil completo do cliente com histórico de atendimentos, próximos atendimentos e ações (agendar, excluir).

**Dependências:** T-150.

**O que fazer:**

1. Crie `app/Livewire/Painel/Clientes/PerfilCliente.php`:
   - Recebe `$clienteId` como parâmetro de URL
   - Carrega o cliente com todos os relacionamentos necessários
   - Método `excluir()` que faz soft delete e redireciona para a lista
2. Atualize a rota de `/pacientes/{cliente}` para usar este componente.

**Resultado esperado:** Perfil do cliente com dados reais, histórico e ações funcionais.

---

---

# FASE 10 — Módulo Financeiro

---

## T-160 — Livewire: FinanceiroIndex

**Objetivo:** Exibir resumo financeiro do período com stat cards, gráfico e tabela de atendimentos.

**Dependências:** T-076, T-112.

**O que fazer:**

1. Crie `app/Livewire/Painel/Financeiro/FinanceiroIndex.php`:
   - Propriedades: `public string $periodo = 'mes_atual'`
   - Método `getPeriodo()` que retorna as datas de início e fim baseado no período selecionado
   - Queries: receita confirmada, a receber (pendente), atendimentos realizados, ticket médio

2. Os dados do gráfico devem ser retornados como JSON para o Chart.js via `wire:init` ou `$this->dispatch('dados-grafico', dados: [...])`.

**Resultado esperado:** Resumo financeiro funcional com filtro de período.

---

## T-161 — Livewire: RegistrarPagamentoModal

**Objetivo:** Modal para registrar o pagamento de um atendimento, criando uma `Transacao`.

**Dependências:** T-160.

**O que fazer:**

1. Crie o componente com formulário: forma de pagamento (Enum `FormaPagamento`), valor recebido, data do pagamento, observação.
2. Ao confirmar:
   - Cria `Transacao` com `tipo = recebimento` e `status = confirmado`
   - Atualiza `sessao.status_pagamento = pago`
3. Use `DB::transaction()` para garantir que os dois registros sejam consistentes.

**Resultado esperado:** Pagamento registrado aparece na tabela de transações e o status do atendimento muda.

---

## T-162 — Livewire: HistoricoFinanceiro

**Objetivo:** Exibir histórico completo de transações com filtros avançados.

**Dependências:** T-160.

**O que fazer:**

Crie o componente com filtros por: período, tipo de transação, forma de pagamento, cliente. Implemente exportação para CSV usando `response()->streamDownload()`.

**Resultado esperado:** Histórico paginado e exportável.

---

---

# FASE 11 — Módulo Automações

---

## T-170 — Livewire: AutomacoesIndex

**Objetivo:** Listar as automações configuradas, com toggle de ativo/inativo e modal de edição.

**Dependências:** T-078, T-112.

**O que fazer:**

1. Crie `app/Livewire/Painel/Automacoes/AutomacoesIndex.php`.
2. Implemente `toggleAtivo(int $automacaoId)` que alterna o campo `ativo`.
3. Crie o subcomponente `ConfigurarAutomacaoModal` com formulário de edição do template de mensagem.

**Resultado esperado:** Toggle de automações funcional.

---

## T-171 — Livewire: StatusWhatsapp

**Objetivo:** Exibir o status da conexão WhatsApp e oferecer ações de reconectar/desconectar.

**Dependências:** T-077.

**O que fazer:**

Crie o componente que lê `conexoes_whatsapp` e exibe o status atual. O botão "Reconectar" simula a geração de QR Code (a integração real com a API do WhatsApp é trabalho futuro — por ora, apenas atualize o campo `status`).

**Resultado esperado:** Status de conexão exibido com dados reais.

---

---

# FASE 12 — Módulo Configurações

---

## T-180 a T-186 — Livewire: Abas de Configurações

**Objetivo:** Implementar cada aba da tela de configurações como um componente Livewire independente.

**Dependências:** T-069, T-067, T-071, T-063.

**O que fazer para cada aba:**

| Task | Componente | O que implementa |
|---|---|---|
| T-180 | `ConfigHorarios` | CRUD de `horarios_atendimento` (7 dias da semana) |
| T-181 | `ConfigServicos` | CRUD de `servicos` com toggle de agendamento online |
| T-182 | `ConfigCancelamentos` | CRUD de `politicas_cancelamento` |
| T-183 | `ConfigConta` | Edição de `profissional` (nome, especialidade, bio, foto) |
| T-184 | `ConfigAssinatura` | Exibir plano atual, datas de fatura, botão de cancelamento |
| T-185 | `ConfigPermissoes` | Exibir papéis dos membros da equipe com toggle de acesso |

**Resultado esperado:** Cada aba salva e reflete os dados corretamente.

---

---

# FASE 13 — Módulo Equipe

---

## T-190 — Livewire: GerenciarEquipe

**Objetivo:** Listar membros da equipe, exibir convites pendentes e ação de remover membro.

**Dependências:** T-065, T-066.

**O que fazer:**

1. Crie `app/Livewire/Painel/Equipe/GerenciarEquipe.php`.
2. Carregue membros ativos e convites pendentes (não expirados).
3. Método `removerMembro(int $membroId)` que faz soft delete em `membros_equipe`.

**Resultado esperado:** Lista de equipe com dados reais.

---

## T-191 — Livewire: ConvidarMembroModal e fluxo de aceite

**Objetivo:** Implementar o fluxo completo de convite: envio do e-mail → link com token → aceite.

**Dependências:** T-190.

**O que fazer:**

1. Crie o modal com campo de e-mail e seleção de papel.
2. Ao enviar: valida limite de assentos do plano → cria registro em `convites` com token único → envia e-mail com o link.
3. Crie `app/Http/Controllers/AceitarConviteController.php` que:
   - Recebe o token via URL
   - Valida que o token existe, não está expirado e não foi aceito
   - Se o e-mail já tem conta: vincula o usuário existente a `membros_equipe` e marca `aceito_em`
   - Se não tem conta: redireciona para cadastro com e-mail pré-preenchido

**Resultado esperado:** Fluxo completo de convite funcional de ponta a ponta.

---

---

# FASE 14 — Página Pública e Agendamento Online

---

## T-200 — Controller: PaginaPublicaController

**Objetivo:** Exibir a página pública do profissional para visitantes não autenticados.

**Dependências:** T-080, T-067.

**O que fazer:**

1. Crie `app/Http/Controllers/PaginaPublicaController.php`:
   - Método `show(string $slug)` que busca a `PaginaPublica` pelo slug (sem Global Scope, pois é pública)
   - Retorna 404 se `publicada = false`
   - Carrega serviços com `permite_agendamento_online = true` e `ativo = true`
2. Atualize a rota `/p/{slug}` para usar este controller.

**Resultado esperado:** Página pública acessível via `/p/{slug}`.

---

## T-201 — Livewire: Fluxo de Agendamento Online

**Objetivo:** Implementar as 3 etapas do agendamento público: escolha do serviço → escolha do horário → confirmação.

**Dependências:** T-200, T-131.

**O que fazer:**

1. Crie `app/Livewire/Agendamento/EtapaInicio.php`:
   - Exibe serviços disponíveis para agendamento
   - Ao selecionar serviço, redireciona para etapa de horário

2. Crie `app/Livewire/Agendamento/EtapaHorario.php`:
   - Exibe calendário com dias disponíveis
   - Ao selecionar dia, usa `SlotCalculator` para listar horários disponíveis

3. Crie `app/Livewire/Agendamento/EtapaConfirmacao.php`:
   - Formulário com: nome, WhatsApp, e-mail (opcional), mensagem (opcional), aceite LGPD
   - Ao enviar: valida `lgpd_aceito = true` → cria `SolicitacaoAgendamento` → exibe mensagem de sucesso

**Conceito-chave:** O fluxo de agendamento é público — não há usuário autenticado. Por isso, os componentes Livewire aqui **não** usam a trait `BelongsToProfissional` nem o Global Scope. O `profissional_id` é resolvido a partir do slug da página.

**Resultado esperado:** Fluxo de agendamento completo funcional do ponto de vista do cliente.

---

## T-202 — Livewire: Fila de Aprovação de Agendamentos

**Objetivo:** Permitir que o profissional aprove ou rejeite solicitações de agendamento recebidas.

**Dependências:** T-201.

**O que fazer:**

1. Adicione uma seção no dashboard ou nas configurações listando `solicitacoes_agendamento` com `status = pendente`.
2. Método `aprovar(int $solicitacaoId)`:
   - Verifica disponibilidade no horário solicitado
   - Cria a `Sessao`
   - Cria ou localiza o `Cliente` pelo WhatsApp
   - Atualiza `solicitacao.status = aprovado` e `solicitacao.sessao_id`
   - Usa `DB::transaction()`
3. Método `rejeitar(int $solicitacaoId)`: atualiza `status = rejeitado` e `respondido_em`.

**Resultado esperado:** Fluxo de aprovação completo.

---

## T-203 — Livewire: EditorPaginaPublica

**Objetivo:** Permitir que o profissional edite sua página pública (slug, bio, serviços visíveis, tags).

**Dependências:** T-080.

**O que fazer:**

Crie `app/Livewire/Painel/PaginaPublica/EditorPagina.php` com:
- Edição de todos os campos de `paginas_publicas`
- Validação de slug único (exceto o próprio registro)
- Toggle `publicada` com aviso de que a página ficará acessível publicamente
- Preview link para `/p/{slug}`

**Resultado esperado:** Editor funcional com preview.

---

---

# FASE 15 — Jobs e Tarefas Agendadas

> **Objetivo da fase:** Implementar os processos automáticos que rodam em background sem interação do usuário.

> 📌 **Conceito fundamental — Jobs e Queues:** Um Job é uma tarefa que pode ser executada em background (fora do ciclo de uma requisição HTTP). Você usa jobs para tarefas demoradas ou recorrentes. O Laravel usa uma tabela `filas_trabalho` para enfileirar jobs. O comando `php artisan queue:work` processa essa fila.

---

## T-220 — Job: GerarSessoesDaRecorrencia

**Objetivo:** Gerar automaticamente os atendimentos futuros de recorrências ativas, com até 60 dias de antecedência.

**Dependências:** T-074, T-075, T-130.

**O que fazer:**

1. Crie:
   ```bash
   php artisan make:job GerarSessoesDaRecorrencia
   ```
2. Na classe, implemente:
   - Busca todas as `Recorrencia` com `ativo = true` (sem Global Scope)
   - Para cada recorrência, calcula as datas dos próximos atendimentos até 60 dias à frente baseado na `frequencia` e `dia_semana`
   - Para cada data, verifica se o atendimento já existe (evita duplicata) e se há disponibilidade
   - Cria os atendimentos que faltam, copiando `politica_cancelamento_id` da recorrência

3. Registre no scheduler em `routes/console.php`:
   ```php
   Schedule::job(new GerarSessoesDaRecorrencia)->dailyAt('06:00');
   ```

**Resultado esperado:** Job cria atendimentos futuros corretamente sem duplicatas.

---

## T-221 — Job: ExpirarSolicitacoesAgendamento

**Objetivo:** Marcar como `expirado` as solicitações de agendamento que passaram do prazo sem resposta.

**Dependências:** T-081.

**O que fazer:**

1. Crie o job:
   ```bash
   php artisan make:job ExpirarSolicitacoesAgendamento
   ```
2. Lógica: `SolicitacaoAgendamento::semEscopo()->where('status', 'pendente')->where('expira_em', '<', now())->update(['status' => 'expirado'])`.
3. Agende para rodar diariamente à meia-noite.

**Resultado esperado:** Solicitações expiradas têm status atualizado.

---

## T-222 — Job: LembretesAtendimentos

**Objetivo:** Disparar mensagens automáticas de lembrete para atendimentos futuros.

**Dependências:** T-078, T-079, T-073.

**O que fazer:**

1. Crie o job que:
   - Busca automações com `tipo IN ('lembrete_48h', 'lembrete_dia')` e `ativo = true`
   - Para `lembrete_48h`: busca sessões que começam entre 47h e 49h à frente
   - Para `lembrete_dia`: busca sessões do dia atual cuja `hora_disparo` já passou
   - Para cada atendimento e automação, verifica em `logs_automacao` se já foi enviado (evita duplicata)
   - Verifica `consentimentos_cliente` com `tipo = receber_whatsapp` e `concedido = true`
   - Se tudo ok, cria o `LogAutomacao` com `status = pendente` (o envio real é delegado a outro serviço)

2. Agende para rodar a cada hora.

**Resultado esperado:** Logs de automação criados corretamente sem duplicatas.

---

## T-223 — Job: PurgarDadosAntigos

**Objetivo:** Excluir definitivamente registros além do período de retenção definido na documentação (seção 18).

**Dependências:** T-079, T-082.

**O que fazer:**

Crie jobs separados (ou um job com múltiplos métodos) para:
- `PurgarLogsAutomacao`: exclui `logs_automacao` com `criado_em < now()->subDays(90)`
- `PurgarLogsAuditoria`: exclui `logs_auditoria` com `criado_em < now()->subYear()`
- `PurgarConvitesExpirados`: exclui `convites` não aceitos com `expira_em < now()`

Agende cada um com a frequência adequada (semanal, mensal).

**Resultado esperado:** Jobs de purga funcionais e agendados.

---

---

# FASE 16 — Observers e Auditoria

> **Objetivo da fase:** Implementar o registro automático de ações sensíveis em `logs_auditoria`.

> 📌 **Conceito fundamental — Eloquent Observer:** Um Observer é uma classe que "escuta" eventos do Eloquent (created, updated, deleted, restored) e executa código automaticamente. É a forma mais elegante de implementar logging de auditoria sem poluir a lógica de negócio.

---

## T-230 — Observer: LogAuditoriaObserver

**Objetivo:** Registrar automaticamente ações críticas no sistema em `logs_auditoria`.

**Dependências:** T-082.

**O que fazer:**

1. Crie:
   ```bash
   php artisan make:observer LogAuditoriaObserver --model=Cliente
   ```
2. Implemente os métodos `created`, `updated`, `deleted`, `restored`:
   ```php
   public function updated(Cliente $cliente): void
   {
       $dirty = $cliente->getDirty();       // campos alterados
       $original = $cliente->getOriginal(); // valores antes da alteração

       // Remove campos sensíveis de autenticação
       $camposIgnorados = ['senha', 'remember_token'];

       LogAuditoria::create([
           'profissional_id' => session('profissional_id_ativo'),
           'usuario_id' => auth()->id(),
           'tipo_entidade' => 'clientes',
           'id_entidade' => $cliente->id,
           'acao' => AcaoAuditoria::Atualizou->value,
           'dados_anteriores' => array_diff_key($original, array_flip($camposIgnorados)),
           'dados_novos' => array_diff_key($dirty, array_flip($camposIgnorados)),
           'ip' => request()->ip(),
       ]);
   }
   ```
3. Registre o observer no `AppServiceProvider`:
   ```php
   Cliente::observe(LogAuditoriaObserver::class);
   Sessao::observe(LogAuditoriaObserver::class);
   Transacao::observe(LogAuditoriaObserver::class);
   ```

**Resultado esperado:** Toda alteração em `clientes`, `sessoes` e `transacoes` gera um log de auditoria.

---

---

# FASE 17 — Testes

> **Objetivo da fase:** Escrever testes automatizados para as funcionalidades mais críticas. Testes garantem que mudanças futuras não quebrem o que já funciona.

> 📌 **Conceito fundamental — Testes no Laravel:** O Laravel vem com PHPUnit integrado. Testes de Feature simulam uma requisição HTTP real ao sistema. Testes de Unit testam uma classe ou método de forma isolada. Use `php artisan test` para rodar todos os testes.

---

## T-240 — Testes: Autenticação

**Objetivo:** Garantir que o fluxo de login e cadastro funciona corretamente.

**Dependências:** T-091.

**O que fazer:**

Crie `tests/Feature/Auth/LoginTest.php`:
- `test_usuario_pode_fazer_login_com_credenciais_corretas()`
- `test_usuario_nao_pode_fazer_login_com_senha_errada()`
- `test_cadastro_cria_usuario_profissional_e_assinatura()`

**Resultado esperado:** `php artisan test --filter=LoginTest` passa todos os testes.

---

## T-241 — Testes: Proteção contra Double-booking

**Objetivo:** Verificar que o sistema impede a criação de atendimentos sobrepostos.

**Dependências:** T-130.

**O que fazer:**

Crie `tests/Feature/Agenda/SobreposicaoTest.php`:
- `test_nao_permite_criar_sessao_no_mesmo_horario()`
- `test_permite_criar_sessao_em_horario_diferente()`
- `test_trigger_impede_sobreposicao_mesmo_com_race_condition()`

**Resultado esperado:** Tentativa de criar atendimento sobreposto lança exceção.

---

## T-242 — Testes: SlotCalculator

**Objetivo:** Verificar que o calculador de slots retorna os horários corretos.

**Dependências:** T-131.

**O que fazer:**

Crie `tests/Unit/SlotCalculatorTest.php`:
- `test_retorna_slots_corretos_em_dia_sem_sessoes()`
- `test_exclui_slots_ocupados_por_sessoes_existentes()`
- `test_exclui_slots_dentro_do_periodo_de_almoco()`
- `test_retorna_array_vazio_para_dia_sem_horario_configurado()`

**Resultado esperado:** Todos os cenários de cálculo de slot passam.

---

## T-243 — Testes: Multi-tenancy (Global Scope)

**Objetivo:** Garantir que um profissional nunca vê dados de outro profissional.

**Dependências:** T-083.

**O que fazer:**

Crie `tests/Feature/Seguranca/TenancyTest.php`:
- Crie dois profissionais com dados distintos
- Autentique como profissional 1
- `test_profissional_nao_ve_clientes_do_outro_profissional()`
- `test_profissional_nao_ve_sessoes_do_outro_profissional()`
- `test_acesso_direto_por_id_retorna_404_para_dados_de_outro_profissional()`

**Resultado esperado:** Isolamento de dados garantido em todos os cenários.

---

## T-244 — Testes: Financeiro

**Objetivo:** Verificar que o registro de pagamentos funciona corretamente e atualiza o status do atendimento.

**Dependências:** T-161.

**O que fazer:**

Crie `tests/Feature/Financeiro/RegistrarPagamentoTest.php`:
- `test_registrar_pagamento_cria_transacao_e_atualiza_sessao()`
- `test_estorno_cria_nova_transacao_sem_editar_original()`
- `test_transacao_original_nao_pode_ser_editada()`

**Resultado esperado:** Todos os testes financeiros passam.

---

---

## Checklist Final de Entrega

Antes de considerar o projeto pronto para produção, verifique:

- [ ] `php artisan test` — todos os testes passam
- [ ] `php artisan migrate:fresh --seed` — banco recriado do zero sem erros
- [ ] Login e cadastro funcionais com criação de `usuario + profissional + assinatura`
- [ ] Global Scope testado: profissional A não vê dados do profissional B
- [ ] Trigger de sobreposição testada: dois atendimentos no mesmo horário geram erro
- [ ] Agenda do dia: criar, confirmar, marcar como realizado, cancelar atendimento
- [ ] Financeiro: registrar pagamento atualiza `status_pagamento` do atendimento
- [ ] Jobs agendados configurados no scheduler (`routes/console.php`)
- [ ] `.env.example` atualizado com todas as variáveis necessárias
- [ ] `config/session.php` usando tabela `http_sessions`
- [ ] Nenhum dado hardcoded nas views (todos os arrays PHP foram substituídos por queries)

---

*Documento gerado com base na documentação do banco `docs/banco-de-dados.md` v1.1 e nas views de `resources/views/`. Atualizar este documento sempre que houver mudança no schema ou nos requisitos de negócio.*
