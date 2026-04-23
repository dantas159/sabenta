# Documentação do Banco de Dados — Sabenta

**Versão:** 1.1  
**Data:** Abril de 2026 (revisado em 21/04/2026)  
**Banco:** MySQL 8.0 (InnoDB)  
**Charset:** utf8mb4 / Collation: utf8mb4_unicode_ci

---

## Sumário

1. [Visão Geral](#1-visão-geral)
2. [Arquitetura e Modelo de Tenancy](#2-arquitetura-e-modelo-de-tenancy)
3. [Privacidade e LGPD](#3-privacidade-e-lgpd)
4. [Diagrama de Relacionamentos](#4-diagrama-de-relacionamentos)
5. [Tabelas — Autenticação e Usuários](#5-tabelas--autenticação-e-usuários)
6. [Tabelas — Planos e Assinatura](#6-tabelas--planos-e-assinatura)
7. [Tabelas — Profissional e Equipe](#7-tabelas--profissional-e-equipe)
8. [Tabelas — Clientes](#8-tabelas--clientes)
9. [Tabelas — Agenda](#9-tabelas--agenda)
10. [Tabelas — Financeiro](#10-tabelas--financeiro)
11. [Tabelas — Automações WhatsApp](#11-tabelas--automações-whatsapp)
12. [Tabelas — Página Pública e Agendamento](#12-tabelas--página-pública-e-agendamento)
13. [Tabelas — Auditoria](#13-tabelas--auditoria)
14. [Estratégia de Indexação](#14-estratégia-de-indexação)
15. [Proteção contra Sobreposição de Horários](#15-proteção-contra-sobreposição-de-horários)
16. [Regras de Negócio Consolidadas](#16-regras-de-negócio-consolidadas)
17. [Schemas JSON dos Campos Complexos](#17-schemas-json-dos-campos-complexos)
18. [Política de Retenção de Dados](#18-política-de-retenção-de-dados)
19. [Ordem de Criação das Tabelas](#19-ordem-de-criação-das-tabelas)
20. [Glossário](#20-glossário)

---

## 1. Visão Geral

O Sabenta é um SaaS de agendamento e gestão financeira para **profissionais autônomos**. O sistema permite que um profissional gerencie sua agenda, seus clientes, suas finanças e sua página pública de agendamento online — com a possibilidade de adicionar um membro de equipe (secretaria/assistente) conforme o plano contratado.

### Escopo do sistema

| Módulo | Responsabilidade |
|---|---|
| Autenticação | Login, cadastro, redefinição de senha |
| Profissional | Perfil, configurações, disponibilidade |
| Equipe | Membros adicionais por assentos do plano |
| Clientes | Cadastro operacional de clientes |
| Agenda | Atendimentos, recorrências, bloqueios |
| Financeiro | Transações, pagamentos, histórico |
| Automações | Mensagens automáticas via WhatsApp |
| Página Pública | Perfil público e agendamento online |
| Assinatura | Planos, faturas, ciclo de cobrança |
| Auditoria | Rastreabilidade de ações sensíveis |

### Totais do banco

| Categoria | Quantidade |
|---|---|
| Tabelas de domínio | 23 |
| Tabelas de infraestrutura Laravel | 6 |
| **Total de tabelas** | **29** |

---

## 2. Arquitetura e Modelo de Tenancy

### Profissional como tenant

**O Sabenta não possui o conceito de clínica.** A unidade central do sistema é o `profissional`. Cada profissional é um tenant independente — seus dados (clientes, atendimentos, finanças, automações) são completamente isolados dos demais.

O isolamento é garantido por:

- Chave estrangeira `profissional_id` presente em todas as tabelas de domínio
- Global Scope no Eloquent que injeta automaticamente `profissional_id` em todas as queries
- A sessão autenticada sempre carrega o `profissional_id` ativo
- Nenhuma tabela de domínio possui dados globais ou compartilhados entre profissionais

### Modelo de assentos (seats)

O plano contratado define o número máximo de usuários que podem operar na conta do profissional. O profissional é sempre o usuário principal. Usuários adicionais (secretaria, assistente) são membros de equipe vinculados à conta.

```
Plano Solo   → 1 assento  → apenas o profissional
Plano Dupla  → 2 assentos → profissional + 1 membro de equipe
Plano Equipe → 5 assentos → profissional + até 4 membros de equipe
```

> 📌 **Decisão de design:** A coluna `limite_assentos` em `planos` controla quantos usuários podem estar ativos simultaneamente na conta. O sistema verifica esse limite antes de aceitar novos convites em `convites`.

### Fluxo de acesso de um membro de equipe

```
usuario (secretaria)
    └── membros_equipe.usuario_id
            └── membros_equipe.profissional_id
                    └── profissional (titular da conta)
```

O membro de equipe opera sempre no contexto do profissional ao qual está vinculado. Não há login "neutro" — ao entrar, o sistema resolve imediatamente o `profissional_id` associado ao usuário.

---

## 3. Privacidade e LGPD

O Sabenta coleta e processa **dados pessoais comuns** (nome, e-mail, telefone) de clientes do profissional. Não há coleta de dados sensíveis de saúde.

### Dados coletados por categoria

| Categoria | Dado | Tabela | Base legal (LGPD) |
|---|---|---|---|
| Identificação do profissional | Nome, e-mail | `usuarios`, `profissionais` | Execução de contrato |
| Contato do cliente | Nome, WhatsApp, e-mail | `clientes` | Legítimo interesse + consentimento |
| Agendamento | Data, horário, serviço | `sessoes` | Execução de contrato |
| Consentimento WhatsApp | Aceite de mensagens | `consentimentos_cliente` | Consentimento |
| Dados de cobrança | Valor, forma de pagamento | `transacoes` | Execução de contrato |
| Logs de acesso | IP, user agent | `logs_auditoria` | Legítimo interesse (segurança) |

### Obrigações implementadas no banco

- **Exclusão lógica (soft delete):** tabelas críticas têm `excluido_em` para preservar integridade referencial antes da exclusão definitiva.
- **Consentimento registrado:** `consentimentos_cliente` armazena o aceite de WhatsApp antes de qualquer automação ser disparada.
- **Portabilidade:** o esquema permite exportação completa de todos os dados de um `profissional_id` em consultas diretas.
- **Direito ao esquecimento:** após cancelamento da assinatura e período de carência, todos os registros do profissional são excluídos definitivamente por job agendado.

---

## 4. Diagrama de Relacionamentos

```
usuarios ─────────────── profissionais (1:1)
                              │
              ┌───────────────┼──────────────────────────┐
              │               │                          │
        membros_equipe    assinaturas               paginas_publicas
              │               │                          │
           usuarios        faturas              solicitacoes_agendamento
                              
profissionais ─┬─ servicos
               ├─ salas
               ├─ horarios_atendimento
               ├─ bloqueios_agenda
               ├─ politicas_cancelamento
               ├─ clientes ──── consentimentos_cliente
               │       │
               │    sessoes ─┬─ servicos
               │       │     ├─ salas
               │       │     └─ recorrencias
               │       │
               │  transacoes
               │
               ├─ conexoes_whatsapp
               ├─ automacoes ── logs_automacao
               └─ logs_auditoria
```

---

## 5. Tabelas — Autenticação e Usuários

### 5.1 `usuarios`

**Propósito:** Credenciais de acesso ao sistema. Um usuário pode ser um profissional titular ou um membro de equipe (secretaria).

**Nível de sensibilidade:** Sensível (dados de autenticação)

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `nome` | VARCHAR(150) | Não | — | Nome completo |
| `email` | VARCHAR(255) | Não | — | E-mail único, usado como login |
| `email_verificado_em` | TIMESTAMP | Sim | NULL | Data de verificação do e-mail |
| `senha` | VARCHAR(255) | Não | — | Hash bcrypt |
| `remember_token` | VARCHAR(100) | Sim | NULL | Token de sessão persistente |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | Chave primária |
| `idx_usuarios_email` | `(email)` | UNIQUE | Login e busca |
| `idx_usuarios_excluidos_purge` | `(excluido_em)` | INDEX | Job de purga |

**Regras de negócio:**
- O e-mail deve ser único em toda a tabela, independentemente de o usuário ser profissional ou membro de equipe.
- A exclusão de um usuário é sempre lógica (soft delete). A exclusão definitiva ocorre apenas após o cancelamento da assinatura e encerramento do período de carência.
- Ao excluir logicamente um usuário que é profissional titular, o sistema deve excluir logicamente todos os dados de domínio associados a ele.

---

### 5.2 `tokens_redefinicao_senha`

**Propósito:** Armazena tokens temporários para fluxo de redefinição de senha. Tabela padrão do Laravel (renomeada).

**Nível de sensibilidade:** Sensível

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `email` | VARCHAR(255) | Não | — | PK e referência ao e-mail do usuário |
| `token` | VARCHAR(255) | Não | — | Hash do token enviado por e-mail |
| `criado_em` | TIMESTAMP | Sim | NULL | Data de geração |

**Regras de negócio:**
- Token válido por 60 minutos (configurável em `config/auth.php`).
- Apenas um token ativo por e-mail. Novo pedido sobrescreve o anterior.

---

### 5.3 Tabelas de infraestrutura Laravel

As tabelas a seguir são gerenciadas pelo framework e não fazem parte do domínio do negócio. São documentadas apenas para referência:

> ⚠️ A tabela de sessões HTTP deve ser configurada com o nome `http_sessions` em `config/session.php` para evitar colisão de nomenclatura com a tabela de domínio `sessoes`. Nunca use o nome padrão `sessions` ou qualquer variante em português neste projeto.

| Tabela | Finalidade |
|---|---|
| `http_sessions` | Sessões autenticadas (driver `database`) — renomeada de `sessions` para evitar conflito com `sessoes` |
| `cache` | Cache da aplicação (driver `database`) |
| `cache_locks` | Locks distribuídos para cache |
| `filas_trabalho` | Jobs em fila para processamento assíncrono |
| `lotes_trabalho` | Controle de job batches |
| `trabalhos_falhados` | Jobs que falharam após todas as tentativas |

---

## 6. Tabelas — Planos e Assinatura

### 6.1 `planos`

**Propósito:** Define os planos de assinatura disponíveis. Cada plano estabelece o número de assentos (usuários simultâneos) e os recursos habilitados.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `nome` | VARCHAR(100) | Não | — | Nome do plano (ex: "Solo", "Dupla", "Equipe") |
| `slug` | VARCHAR(50) | Não | — | Identificador URL-safe único e imutável |
| `descricao` | TEXT | Sim | NULL | Descrição de marketing |
| `preco_mensal` | DECIMAL(10,2) | Não | — | Preço no período mensal |
| `preco_anual` | DECIMAL(10,2) | Sim | NULL | Preço no período anual (pode ser NULL se não oferecido) |
| `limite_assentos` | TINYINT UNSIGNED | Não | 1 | Número máximo de usuários simultâneos na conta |
| `limite_clientes` | INT UNSIGNED | Sim | NULL | Limite de clientes cadastrados (NULL = ilimitado) |
| `recursos` | JSON | Não | — | Feature flags do plano (ver schema em §17) |
| `ativo` | TINYINT(1) | Não | 1 | Plano disponível para novas assinaturas |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_planos_slug` | `(slug)` | UNIQUE | Busca por identificador |
| `idx_planos_ativos` | `(ativo)` | INDEX | Listagem da página de planos |

**Regras de negócio:**
- `slug` é definido na criação e nunca alterado, pois pode ser referenciado em URLs e integrações.
- Planos inativos (`ativo = 0`) não aparecem para novos clientes, mas continuam válidos para assinaturas existentes.
- O campo `recursos` controla acesso a funcionalidades opcionais: agendamento online, automações WhatsApp, múltiplas salas, relatórios avançados.

---

### 6.2 `assinaturas`

**Propósito:** Registro do contrato entre o profissional e o Sabenta. Uma assinatura ativa é pré-requisito para o acesso ao painel.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `plano_id` | BIGINT UNSIGNED | Não | — | FK → `planos.id` |
| `status` | ENUM | Não | `'trial'` | Estado da assinatura |
| `periodo` | ENUM | Não | `'mensal'` | Ciclo de cobrança |
| `trial_termina_em` | DATE | Sim | NULL | Data de encerramento do período de teste |
| `inicio_em` | DATE | Não | — | Início da assinatura paga |
| `termina_em` | DATE | Sim | NULL | Data de expiração (NULL = recorrente ativa) |
| `cancelado_em` | TIMESTAMP | Sim | NULL | Data do cancelamento, se aplicável |
| `gateway_id` | VARCHAR(255) | Sim | NULL | ID da assinatura no gateway de pagamento |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `status`:**

```
'trial'         → Período de teste ativo, sem cobrança
'ativa'         → Assinatura paga e em dia
'inadimplente'  → Fatura vencida, acesso restrito
'cancelada'     → Cancelada pelo profissional ou pelo sistema
'expirada'      → Período encerrado sem renovação
```

**Valores do ENUM `periodo`:**

```
'mensal'  → Cobrança mensal
'anual'   → Cobrança anual com desconto
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_assinaturas_profissional` | `(profissional_id, status)` | INDEX | Verificação de acesso |
| `idx_assinaturas_vencimento` | `(termina_em, status)` | INDEX | Job de expiração |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |
| `plano_id` | `planos.id` | RESTRICT |

**Regras de negócio:**
- Cada profissional pode ter apenas **uma assinatura ativa** por vez. O sistema valida isso na camada de aplicação antes de criar uma nova.
- Durante o `trial`, o profissional tem acesso ao plano selecionado sem cobrança. Ao expirar o trial sem pagamento, o status muda para `expirada`.
- A mudança de plano (upgrade/downgrade) encerra a assinatura atual e cria uma nova, gerando fatura proporcional se necessário.

---

### 6.3 `faturas`

**Propósito:** Registro de cada cobrança gerada pela assinatura. Imutável por design — cancelamentos geram nova fatura do tipo estorno, nunca edição.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `assinatura_id` | BIGINT UNSIGNED | Não | — | FK → `assinaturas.id` |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` (desnormalizado para queries) |
| `valor` | DECIMAL(10,2) | Não | — | Valor total da fatura |
| `status` | ENUM | Não | `'aberta'` | Estado da cobrança |
| `vencimento_em` | DATE | Não | — | Data de vencimento |
| `pago_em` | TIMESTAMP | Sim | NULL | Data do pagamento confirmado |
| `gateway_fatura_id` | VARCHAR(255) | Sim | NULL | ID da fatura no gateway externo |
| `gateway_dados` | JSON | Sim | NULL | Payload de resposta do gateway |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `status`:**

```
'aberta'     → Aguardando pagamento
'paga'       → Pagamento confirmado
'vencida'    → Data de vencimento ultrapassada sem pagamento
'cancelada'  → Fatura cancelada (ex: mudança de plano)
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_faturas_profissional` | `(profissional_id, status, vencimento_em)` | INDEX | Painel financeiro da assinatura |
| `idx_faturas_vencidas` | `(status, vencimento_em)` | INDEX | Job de cobrança |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `assinatura_id` | `assinaturas.id` | RESTRICT |
| `profissional_id` | `profissionais.id` | RESTRICT |

> ⚠️ `faturas` não tem `atualizado_em`. Uma vez criada, a fatura é imutável. Mudanças de status são feitas por insert de nova fatura com referência à anterior, ou via campo `status` que é a única coluna mutável permitida.

---

## 7. Tabelas — Profissional e Equipe

### 7.1 `profissionais`

**Propósito:** Perfil completo do profissional autônomo. É a entidade central do sistema — o tenant. Toda leitura e escrita de dados de domínio passa pelo `profissional_id`.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `usuario_id` | BIGINT UNSIGNED | Não | — | FK → `usuarios.id` (relação 1:1) |
| `nome_exibicao` | VARCHAR(150) | Não | — | Nome mostrado em telas e na página pública |
| `especialidade` | VARCHAR(150) | Sim | NULL | Área de atuação (texto livre, definido pelo próprio profissional) |
| `registro_profissional` | VARCHAR(50) | Sim | NULL | Número de registro profissional (ex: OAB 123456, CREA 123456, CRO 12345 — opcional e livre) |
| `bio` | TEXT | Sim | NULL | Texto de apresentação para a página pública |
| `foto_url` | VARCHAR(500) | Sim | NULL | URL da foto de perfil (armazenada em storage externo) |
| `cor_agenda` | VARCHAR(7) | Sim | NULL | Cor hexadecimal usada para identificar o profissional na agenda |
| `duracao_padrao_minutos` | SMALLINT UNSIGNED | Não | 50 | Duração padrão de um atendimento em minutos |
| `valor_padrao` | DECIMAL(10,2) | Sim | NULL | Valor padrão cobrado por atendimento |
| `fuso_horario` | VARCHAR(50) | Não | `'America/Sao_Paulo'` | Fuso horário para cálculo de horários |
| `ativo` | TINYINT(1) | Não | 1 | Conta ativa |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_profissionais_usuario` | `(usuario_id)` | UNIQUE | Garante relação 1:1 com usuarios |
| `idx_profissionais_excluidos` | `(excluido_em)` | INDEX | Job de purga |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `usuario_id` | `usuarios.id` | CASCADE |

**Regras de negócio:**
- Um `usuario` pode ter no máximo **um** `profissional` vinculado. A constraint UNIQUE em `usuario_id` garante isso no banco.
- O campo `registro_profissional` é de preenchimento opcional e livre — o sistema não valida o formato (CRP, CRM, OAB, etc.).
- `duracao_padrao_minutos` e `valor_padrao` são usados como valores sugeridos ao criar um novo atendimento sem serviço específico.
- O soft delete de um profissional deve encadear soft delete em todas as tabelas filhas (clientes, sessoes, etc.) via job assíncrono, não via CASCADE, para evitar lock em tabelas grandes.

---

### 7.2 `membros_equipe`

**Propósito:** Usuários adicionais que operam na conta de um profissional (secretaria, assistente). A existência desse registro é controlada pelo limite de assentos do plano.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` (titular da conta) |
| `usuario_id` | BIGINT UNSIGNED | Não | — | FK → `usuarios.id` (o membro convidado) |
| `papel` | ENUM | Não | `'secretaria'` | Nível de acesso do membro |
| `ativo` | TINYINT(1) | Não | 1 | Acesso ativo |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Valores do ENUM `papel`:**

```
'secretaria'  → Acessa agenda de todos, cadastra clientes e registra pagamentos.
                Sem acesso a financeiro consolidado, configurações ou assinatura.
'assistente'  → Acessa agenda e clientes, mas apenas visualização.
                Não pode criar, editar ou cancelar atendimentos.
'admin'       → Acesso completo à conta, exceto dados de cobrança do Sabenta.
                Pode gerenciar equipe, configurações e página pública.
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_membros_equipe_profissional` | `(profissional_id, ativo)` | INDEX | Listagem de equipe |
| `idx_membros_equipe_usuario` | `(usuario_id)` | INDEX | Resolução de contexto no login |
| `uk_membros_equipe` | `(profissional_id, usuario_id)` | UNIQUE | Evita duplicata de vínculo |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |
| `usuario_id` | `usuarios.id` | CASCADE |

**Regras de negócio:**
- O sistema verifica, antes de aceitar um convite, se `COUNT(membros_equipe WHERE profissional_id = X AND ativo = 1) + 1 < planos.limite_assentos`. Se o limite for atingido, o convite é rejeitado.
- O profissional titular **nunca** aparece em `membros_equipe`. Ele é identificado pela relação direta em `profissionais.usuario_id`.
- Um mesmo usuário (`usuario_id`) pode ser membro de equipe de múltiplos profissionais, desde que cada vínculo tenha sido explicitamente criado por convite.

---

### 7.3 `convites`

**Propósito:** Tokens de convite enviados por e-mail para adicionar membros à equipe do profissional.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `email` | VARCHAR(255) | Não | — | E-mail do convidado |
| `papel` | ENUM | Não | — | Papel que será atribuído ao aceitar |
| `token` | VARCHAR(100) | Não | — | Token único gerado com `Str::random(64)` |
| `aceito_em` | TIMESTAMP | Sim | NULL | Data de aceite (NULL = pendente) |
| `expira_em` | TIMESTAMP | Não | — | Prazo de validade do convite |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_convites_token` | `(token)` | UNIQUE | Busca ao clicar no link do e-mail |
| `idx_convites_expiracao` | `(expira_em, aceito_em)` | INDEX | Job de limpeza de convites expirados |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |

**Regras de negócio:**
- Convite válido por 7 dias a partir de `criado_em`.
- Se o e-mail convidado já possuir uma conta em `usuarios`, o aceite vincula o usuário existente. Caso contrário, redireciona para o cadastro com o e-mail pré-preenchido.
- Convites expirados (`expira_em < NOW() AND aceito_em IS NULL`) são excluídos definitivamente por job semanal.

---

## 8. Tabelas — Clientes

### 8.1 `clientes`

**Propósito:** Cadastro operacional de clientes do profissional. Contém apenas os dados necessários para agendamento e comunicação. Não armazena nenhum dado pessoal sensível além do necessário para contato.

**Nível de sensibilidade:** Sensível (PII — dados de contato)

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `nome` | VARCHAR(150) | Não | — | Nome completo do cliente |
| `whatsapp` | VARCHAR(20) | Sim | NULL | Número no formato E.164 (ex: +5511999999999) |
| `email` | VARCHAR(255) | Sim | NULL | E-mail do cliente |
| `data_nascimento` | DATE | Sim | NULL | Data de nascimento (usada pela automação de aniversário) |
| `como_chegou` | ENUM | Sim | NULL | Canal de origem |
| `observacoes` | TEXT | Sim | NULL | Notas operacionais simples (ex: preferência de horário) |
| `ativo` | TINYINT(1) | Não | 1 | Cliente ativo na agenda |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Valores do ENUM `como_chegou`:**

```
'indicacao'      → Indicação de outro cliente
'google'         → Google ou buscador
'redes_sociais'  → Instagram, Facebook, LinkedIn etc.
'outro'          → Outros canais
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_clientes_profissional_nome` | `(profissional_id, nome, excluido_em)` | INDEX | Busca de clientes na lista |
| `idx_clientes_whatsapp` | `(profissional_id, whatsapp)` | INDEX | Busca por telefone |
| `idx_clientes_aniversario` | `(profissional_id, data_nascimento)` | INDEX | Job de automação de aniversário |
| `idx_clientes_excluidos_purge` | `(excluido_em)` | INDEX | Job de purga |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |

**Regras de negócio:**
- O campo `observacoes` é destinado exclusivamente a informações operacionais (preferência de horário, local de atendimento, forma de pagamento preferida). Não deve ser utilizado para registrar informações de caráter confidencial ou pessoal sensível.
- Um cliente excluído logicamente (`excluido_em IS NOT NULL`) não aparece em listagens nem pode ter novas sessões criadas, mas seu histórico de atendimentos e transações é preservado para integridade financeira.
- O campo `whatsapp` deve armazenar o número no formato E.164 para garantir compatibilidade com a API do WhatsApp.

---

### 8.2 `consentimentos_cliente`

**Propósito:** Registra o aceite do cliente para uso de dados conforme a LGPD. Obrigatório antes do envio de mensagens automáticas via WhatsApp.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `cliente_id` | BIGINT UNSIGNED | Não | — | FK → `clientes.id` |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `tipo` | ENUM | Não | — | Tipo de consentimento |
| `concedido` | TINYINT(1) | Não | — | 1 = aceito, 0 = recusado |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `tipo`:**

```
'termos_uso'        → Aceite dos termos de uso da plataforma
'receber_whatsapp'  → Autorização para receber mensagens automáticas via WhatsApp
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `uk_consentimentos` | `(cliente_id, tipo)` | UNIQUE | Um registro por tipo por cliente |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `cliente_id` | `clientes.id` | CASCADE |
| `profissional_id` | `profissionais.id` | RESTRICT |

**Regras de negócio:**
- A constraint UNIQUE em `(cliente_id, tipo)` garante que há apenas um registro de consentimento por tipo. Para revogar, o sistema atualiza `concedido = 0` no registro existente.
- O sistema de automações verifica `concedido = 1` para o tipo `'receber_whatsapp'` antes de disparar qualquer mensagem ao cliente.
- O consentimento é coletado no formulário de agendamento online (`solicitacoes_agendamento`) e ao cadastrar manualmente via painel.

---

## 9. Tabelas — Agenda

### 9.1 `servicos`

**Propósito:** Tipos de atendimento oferecidos pelo profissional, com duração e valor definidos. Cada atendimento pode ou não estar associado a um serviço.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `nome` | VARCHAR(150) | Não | — | Nome do serviço (ex: "Atendimento Individual") |
| `descricao` | TEXT | Sim | NULL | Descrição exibida na página pública |
| `duracao_minutos` | SMALLINT UNSIGNED | Não | — | Duração fixa do serviço em minutos |
| `valor` | DECIMAL(10,2) | Não | — | Valor cobrado |
| `permite_agendamento_online` | TINYINT(1) | Não | 1 | Exibido na página pública para agendamento |
| `ativo` | TINYINT(1) | Não | 1 | Disponível para uso |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_servicos_profissional` | `(profissional_id, ativo, excluido_em)` | INDEX | Listagem de serviços ativos |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |

---

### 9.2 `salas`

**Propósito:** Locais ou salas de atendimento do profissional. Opcional — usado quando o profissional atende em mais de um espaço físico ou online.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `nome` | VARCHAR(100) | Não | — | Nome ou descrição da sala (ex: "Sala 1", "Online") |
| `descricao` | TEXT | Sim | NULL | Endereço, link de videoconferência ou outros detalhes |
| `ativo` | TINYINT(1) | Não | 1 | Sala disponível para uso |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_salas_profissional` | `(profissional_id, ativo)` | INDEX | Listagem de salas |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |

---

### 9.3 `horarios_atendimento`

**Propósito:** Define a disponibilidade semanal do profissional por dia da semana. É a base para o cálculo de slots livres no agendamento online.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `dia_semana` | TINYINT UNSIGNED | Não | — | 0 = Domingo, 1 = Segunda, ..., 6 = Sábado |
| `hora_inicio` | TIME | Não | — | Início do período de atendimento |
| `hora_fim` | TIME | Não | — | Fim do período de atendimento |
| `intervalo_minutos` | TINYINT UNSIGNED | Não | 60 | Tempo de espaçamento entre atendimentos consecutivos (não é bloqueio explícito — use `almoco_inicio`/`almoco_fim` para bloquear o almoço) |
| `almoco_inicio` | TIME | Sim | NULL | Início do intervalo de almoço — o SlotCalculator exclui slots dentro deste período |
| `almoco_fim` | TIME | Sim | NULL | Fim do intervalo de almoço |
| `ativo` | TINYINT(1) | Não | 1 | Dia de atendimento habilitado |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Constraints:**

| Nome | Definição | Motivo |
|---|---|---|
| `chk_horarios_ordem` | `CHECK (hora_fim > hora_inicio)` | Impede horários invertidos |
| `chk_almoco_ordem` | `CHECK (almoco_fim IS NULL OR almoco_fim > almoco_inicio)` | Impede intervalo de almoço invertido |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `uk_horarios_profissional_dia` | `(profissional_id, dia_semana)` | UNIQUE | Um horário por dia por profissional |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |

**Regras de negócio:**
- O `SlotCalculator` — componente responsável por calcular os horários disponíveis — utiliza esta tabela em conjunto com `sessoes` e `bloqueios_agenda` para determinar slots livres.
- `intervalo_minutos` representa o espaçamento entre slots consecutivos — o sistema oferece o próximo horário somente após `duracao_servico + intervalo_minutos`. Não bloqueia o período explicitamente.
- `almoco_inicio` e `almoco_fim`, quando preenchidos, funcionam como um bloqueio implícito diário: o SlotCalculator exclui qualquer slot que se sobreponha a esse intervalo. Use `bloqueios_agenda` para bloqueios esporádicos; use esses campos para o intervalo de almoço recorrente.

---

### 9.4 `bloqueios_agenda`

**Propósito:** Períodos em que o profissional não está disponível para atendimento (férias, feriados, compromissos pessoais). Bloqueia os slots correspondentes no agendamento online.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `titulo` | VARCHAR(150) | Sim | NULL | Descrição interna do bloqueio (ex: "Férias") |
| `inicio_em` | DATETIME | Não | — | Início do bloqueio |
| `fim_em` | DATETIME | Não | — | Fim do bloqueio |
| `dia_inteiro` | TINYINT(1) | Não | 0 | Quando 1, bloqueia o dia inteiro independente dos horários |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Constraints:**

| Nome | Definição | Motivo |
|---|---|---|
| `chk_bloqueios_ordem` | `CHECK (fim_em > inicio_em)` | Impede bloqueios com datas invertidas |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_bloqueios_profissional_periodo` | `(profissional_id, inicio_em, fim_em, excluido_em)` | INDEX | Verificação de disponibilidade |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |

---

### 9.5 `politicas_cancelamento`

**Propósito:** Define as regras de cancelamento do profissional: antecedência mínima e cobrança por ausência.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `nome` | VARCHAR(150) | Não | — | Nome da política (ex: "Padrão", "Pacote Mensal") |
| `horas_antecedencia` | TINYINT UNSIGNED | Não | 24 | Mínimo de horas antes do atendimento para cancelar sem cobrança |
| `cobra_falta` | TINYINT(1) | Não | 0 | Se 1, cobra percentual em caso de falta |
| `percentual_cobranca` | TINYINT UNSIGNED | Sim | NULL | Percentual do valor cobrado em caso de falta (0–100) |
| `ativo` | TINYINT(1) | Não | 1 | Política disponível para uso |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Constraints:**

| Nome | Definição | Motivo |
|---|---|---|
| `chk_percentual_range` | `CHECK (percentual_cobranca IS NULL OR percentual_cobranca BETWEEN 0 AND 100)` | Limita o percentual ao intervalo válido |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_politicas_profissional` | `(profissional_id, ativo)` | INDEX | Listagem de políticas |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |

---

### 9.6 `recorrencias`

**Propósito:** Define a regra de repetição de atendimentos periódicos. Ao ativar uma recorrência, o sistema cria automaticamente os atendimentos futuros com base na frequência definida.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `cliente_id` | BIGINT UNSIGNED | Não | — | FK → `clientes.id` |
| `servico_id` | BIGINT UNSIGNED | Sim | NULL | FK → `servicos.id` |
| `sala_id` | BIGINT UNSIGNED | Sim | NULL | FK → `salas.id` |
| `politica_cancelamento_id` | BIGINT UNSIGNED | Sim | NULL | FK → `politicas_cancelamento.id` — herdado por todos os atendimentos gerados por esta recorrência |
| `frequencia` | ENUM | Não | — | Cadência de repetição |
| `dia_semana` | TINYINT UNSIGNED | Sim | NULL | Dia fixo (0–6), obrigatório para `semanal` e `quinzenal` |
| `hora_inicio` | TIME | Não | — | Horário fixo de início |
| `duracao_minutos` | SMALLINT UNSIGNED | Não | — | Duração de cada atendimento gerado |
| `valor` | DECIMAL(10,2) | Sim | NULL | Valor fixo (se NULL, usa o valor do serviço) |
| `inicia_em` | DATE | Não | — | Data do primeiro atendimento da série |
| `termina_em` | DATE | Sim | NULL | Data do último atendimento (NULL = sem data de fim) |
| `total_sessoes` | SMALLINT UNSIGNED | Sim | NULL | Alternativa a `termina_em` — limita pelo número de atendimentos |
| `ativo` | TINYINT(1) | Não | 1 | Recorrência ativa (gera novas sessões) |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Valores do ENUM `frequencia`:**

```
'semanal'    → Repete toda semana no mesmo dia e horário
'quinzenal'  → Repete a cada duas semanas no mesmo dia e horário
'mensal'     → Repete uma vez por mês (mesmo dia do mês)
```

**Constraints:**

| Nome | Definição | Motivo |
|---|---|---|
| `chk_recorrencias_datas` | `CHECK (termina_em IS NULL OR termina_em >= inicia_em)` | Impede data de fim anterior ao início |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_recorrencias_profissional` | `(profissional_id, ativo)` | INDEX | Listagem de recorrências ativas |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |
| `cliente_id` | `clientes.id` | RESTRICT |
| `servico_id` | `servicos.id` | SET NULL |
| `sala_id` | `salas.id` | SET NULL |
| `politica_cancelamento_id` | `politicas_cancelamento.id` | SET NULL |

**Regras de negócio:**
- O job `GerarSessoesDaRecorrencia` roda diariamente e cria atendimentos com até 60 dias de antecedência.
- Ao gerar cada atendimento, o job copia `politica_cancelamento_id` da recorrência para o atendimento. Caso seja NULL, o atendimento é criado sem política vinculada.
- Alterar uma recorrência não modifica atendimentos já gerados — apenas os futuros ainda não criados.
- Cancelar uma recorrência (`ativo = 0`) não cancela atendimentos já existentes.

---

### 9.7 `sessoes`

**Propósito:** Registro de cada agendamento confirmado ou a confirmar. É a tabela de maior volume do sistema e o núcleo operacional da agenda.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `cliente_id` | BIGINT UNSIGNED | Não | — | FK → `clientes.id` |
| `servico_id` | BIGINT UNSIGNED | Sim | NULL | FK → `servicos.id` |
| `sala_id` | BIGINT UNSIGNED | Sim | NULL | FK → `salas.id` |
| `recorrencia_id` | BIGINT UNSIGNED | Sim | NULL | FK → `recorrencias.id` |
| `politica_cancelamento_id` | BIGINT UNSIGNED | Sim | NULL | FK → `politicas_cancelamento.id` — define regras de cobrança em caso de falta ou cancelamento |
| `inicio_em` | DATETIME | Não | — | Data e hora de início |
| `fim_em` | DATETIME | Não | — | Data e hora de fim |
| `status` | ENUM | Não | `'agendado'` | Estado do agendamento |
| `status_pagamento` | ENUM | Não | `'pendente'` | Estado do pagamento |
| `valor` | DECIMAL(10,2) | Sim | NULL | Valor cobrado neste atendimento |
| `observacoes` | TEXT | Sim | NULL | Observações operacionais (ex: endereço, link de reunião) |
| `lembrete_enviado` | TINYINT(1) | Não | 0 | Lembrete automático disparado |
| `confirmacao_enviada` | TINYINT(1) | Não | 0 | Mensagem de confirmação disparada |
| `cancelado_em` | TIMESTAMP | Sim | NULL | Data do cancelamento |
| `motivo_cancelamento` | TEXT | Sim | NULL | Razão do cancelamento (texto livre) |
| `criado_por` | BIGINT UNSIGNED | Sim | NULL | FK → `usuarios.id` (quem criou o atendimento) |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `excluido_em` | TIMESTAMP | Sim | NULL | Soft delete |

**Valores do ENUM `status`:**

```
'agendado'   → Atendimento criado, aguardando confirmação do cliente
'confirmado' → Cliente confirmou presença
'realizado'  → Atendimento ocorreu
'faltou'     → Cliente não compareceu sem aviso
'cancelado'  → Cancelado por qualquer parte
```

**Valores do ENUM `status_pagamento`:**

```
'pendente'     → Pagamento ainda não recebido
'pago'         → Pagamento confirmado
'isento'       → Atendimento sem cobrança (ex: reposição)
'reembolsado'  → Valor devolvido ao cliente
```

**Constraints:**

| Nome | Definição | Motivo |
|---|---|---|
| `chk_sessoes_ordem_horario` | `CHECK (fim_em > inicio_em)` | Impede atendimento com fim antes do início |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_sessoes_agenda_dia` | `(profissional_id, inicio_em, excluido_em)` | INDEX | Query principal da agenda do dia — mais executada do sistema |
| `idx_sessoes_dashboard` | `(profissional_id, inicio_em, status)` | INDEX | Cards de resumo do dashboard |
| `idx_sessoes_financeiro` | `(profissional_id, status_pagamento, inicio_em)` | INDEX | Relatório financeiro por período |
| `idx_sessoes_historico_cliente` | `(cliente_id, status, inicio_em)` | INDEX | Histórico do cliente |
| `idx_sessoes_sobreposicao` | `(profissional_id, inicio_em, fim_em, status, excluido_em)` | INDEX | Verificação de sobreposição pela trigger |
| `idx_sessoes_purge` | `(excluido_em)` | INDEX | Job de purga de registros antigos |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |
| `cliente_id` | `clientes.id` | RESTRICT |
| `servico_id` | `servicos.id` | SET NULL |
| `sala_id` | `salas.id` | SET NULL |
| `recorrencia_id` | `recorrencias.id` | SET NULL |
| `politica_cancelamento_id` | `politicas_cancelamento.id` | SET NULL |
| `criado_por` | `usuarios.id` | SET NULL |

**Regras de negócio:**
- Todo atendimento com `status != 'cancelado'` participa da verificação de sobreposição (ver §15).
- Ao registrar `status = 'realizado'`, o sistema verifica `status_pagamento` e, se `'pendente'`, pode disparar automação de cobrança.
- Quando `status = 'faltou'` e a política vinculada tem `cobra_falta = 1`, o sistema calcula o valor de cobrança com base em `politicas_cancelamento.percentual_cobranca` aplicado sobre `sessoes.valor` e gera uma transação do tipo `'recebimento'` automaticamente.
- Atendimentos com `excluido_em IS NOT NULL` não aparecem na agenda, mas permanecem no banco para integridade financeira das `transacoes` vinculadas.

---

## 10. Tabelas — Financeiro

### 10.1 `transacoes`

**Propósito:** Registro imutável de cada movimentação financeira. Estornos e ajustes são feitos por inserção de novos registros, nunca por edição.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `sessao_id` | BIGINT UNSIGNED | Sim | NULL | FK → `sessoes.id` |
| `cliente_id` | BIGINT UNSIGNED | Sim | NULL | FK → `clientes.id` (desnormalizado para transações sem atendimento vinculado) |
| `tipo` | ENUM | Não | — | Natureza da transação |
| `valor` | DECIMAL(10,2) | Não | — | Valor positivo (o tipo define se é entrada ou saída) |
| `forma_pagamento` | ENUM | Não | — | Meio de pagamento |
| `status` | ENUM | Não | `'pendente'` | Estado do pagamento |
| `referencia_externa` | VARCHAR(255) | Sim | NULL | ID da transação no gateway de pagamento |
| `descricao` | TEXT | Sim | NULL | Descrição livre para contexto |
| `origem_id` | BIGINT UNSIGNED | Sim | NULL | FK → `transacoes.id` (para estornos: aponta à transação original) |
| `log_automacao_id` | BIGINT UNSIGNED | Sim | NULL | FK → `logs_automacao.id` — preenchido quando a transação foi originada por uma cobrança automática via WhatsApp |
| `criado_por` | BIGINT UNSIGNED | Sim | NULL | FK → `usuarios.id` (NULL quando gerada automaticamente pelo sistema) |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | Data imutável do registro |

**Valores do ENUM `tipo`:**

```
'recebimento'  → Entrada de valor (pagamento do cliente)
'estorno'      → Devolução de valor previamente recebido
'ajuste'       → Correção manual de diferença (ex: desconto aplicado)
```

**Valores do ENUM `forma_pagamento`:**

```
'dinheiro'        → Dinheiro em espécie
'pix'             → Transferência via Pix
'cartao_credito'  → Cartão de crédito
'cartao_debito'   → Cartão de débito
'transferencia'   → Transferência bancária (TED/DOC)
'outro'           → Outros meios
```

**Valores do ENUM `status`:**

```
'pendente'    → Aguardando confirmação
'confirmado'  → Recebido e confirmado
'cancelado'   → Transação cancelada
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_transacoes_profissional_periodo` | `(profissional_id, criado_em, status)` | INDEX | Relatório financeiro por período |
| `idx_transacoes_sessao` | `(sessao_id)` | INDEX | Pagamentos vinculados a um atendimento |
| `idx_transacoes_cliente` | `(cliente_id, criado_em)` | INDEX | Histórico financeiro do cliente |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |
| `sessao_id` | `sessoes.id` | SET NULL |
| `cliente_id` | `clientes.id` | SET NULL |
| `origem_id` | `transacoes.id` | SET NULL |
| `log_automacao_id` | `logs_automacao.id` | SET NULL |
| `criado_por` | `usuarios.id` | SET NULL |

> ⚠️ `transacoes` **não possui** `atualizado_em`. É uma tabela append-only. Nenhuma linha deve jamais ser atualizada após a inserção. O `status` é a única exceção permitida, e somente de `'pendente'` para `'confirmado'` ou `'cancelado'`.

---

## 11. Tabelas — Automações WhatsApp

### 11.1 `conexoes_whatsapp`

**Propósito:** Armazena a conexão ativa do profissional com o WhatsApp Business para disparo de mensagens automáticas.

**Nível de sensibilidade:** Sensível (contém dados de sessão do WhatsApp)

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `numero` | VARCHAR(20) | Não | — | Número conectado no formato E.164 |
| `status` | ENUM | Não | `'desconectado'` | Estado atual da conexão |
| `ultimo_ping_em` | TIMESTAMP | Sim | NULL | Último heartbeat recebido do serviço |
| `ativo` | TINYINT(1) | Não | 1 | Conexão habilitada |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `status`:**

```
'desconectado'  → Sem sessão ativa
'conectando'    → QR Code gerado, aguardando scan
'conectado'     → Sessão ativa e funcional
'bloqueado'     → Número bloqueado pelo WhatsApp
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_conexoes_profissional` | `(profissional_id, ativo)` | INDEX | Verificação de conexão ativa |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |

---

### 11.2 `automacoes`

**Propósito:** Regras de mensagens automáticas configuradas pelo profissional. Cada automação define o gatilho e o template da mensagem.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `nome` | VARCHAR(150) | Não | — | Nome identificador (ex: "Lembrete 48h") |
| `tipo` | ENUM | Não | — | Gatilho que dispara a automação |
| `mensagem` | TEXT | Não | — | Template com variáveis `{nome}`, `{data}`, `{hora}`, `{servico}` |
| `gatilho_horas_antes` | SMALLINT UNSIGNED | Sim | NULL | Horas antes do atendimento para o disparo — usado por `lembrete_48h` e similares |
| `hora_disparo` | TIME | Sim | NULL | Horário fixo de disparo — obrigatório para `lembrete_dia` (ex: `08:00`) |
| `ativo` | TINYINT(1) | Não | 1 | Automação habilitada |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `tipo`:**

```
'confirmacao_agendamento'  → Dispara quando um atendimento é criado ou aprovado
'lembrete_48h'             → Dispara 48h antes do atendimento
'lembrete_dia'             → Dispara no dia do atendimento (hora configurada)
'pos_sessao'               → Dispara após o horário de fim do atendimento
'cobranca'                 → Dispara quando atendimento está realizado e pendente de pagamento
'aniversario'              → Dispara no aniversário do cliente (requer data de nascimento)
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_automacoes_profissional` | `(profissional_id, ativo, tipo)` | INDEX | Busca de automações ativas por tipo |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |

**Regras de negócio:**
- `hora_disparo` é obrigatório quando `tipo = 'lembrete_dia'`. Validar na camada de aplicação antes de salvar.
- `gatilho_horas_antes` é obrigatório quando `tipo IN ('lembrete_48h')`. Para `lembrete_dia`, o campo é ignorado — o disparo ocorre na `hora_disparo` do dia do atendimento.
- O tipo `'aniversario'` requer que o cliente tenha `data_nascimento` preenchido em `clientes`. O job de aniversário deve filtrar apenas clientes com esse campo não nulo.

---

### 11.3 `logs_automacao`

**Propósito:** Registro de cada tentativa de envio de mensagem automática. Fundamental para depuração e para evitar re-envios duplicados.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` |
| `automacao_id` | BIGINT UNSIGNED | Sim | NULL | FK → `automacoes.id` |
| `sessao_id` | BIGINT UNSIGNED | Sim | NULL | FK → `sessoes.id` |
| `cliente_id` | BIGINT UNSIGNED | Sim | NULL | FK → `clientes.id` |
| `destinatario` | VARCHAR(20) | Sim | NULL | Número de destino no formato E.164 |
| `mensagem_enviada` | TEXT | Sim | NULL | Conteúdo final da mensagem após substituição de variáveis |
| `status` | ENUM | Não | `'pendente'` | Estado do envio |
| `erro` | TEXT | Sim | NULL | Detalhe do erro em caso de falha |
| `enviado_em` | TIMESTAMP | Sim | NULL | Data e hora do envio confirmado |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `status`:**

```
'pendente'  → Na fila, aguardando envio
'enviado'   → Entregue ao serviço de WhatsApp
'falhou'    → Erro no envio (ver campo `erro`)
'lido'      → Confirmação de leitura recebida (se disponível via API)
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_logs_automacao_recentes` | `(profissional_id, criado_em, status)` | INDEX | Painel de automações — mensagens recentes |
| `idx_logs_automacao_sessao` | `(sessao_id, automacao_id)` | INDEX | Verificação de duplicata antes do envio |
| `idx_logs_automacao_purge` | `(criado_em)` | INDEX | Job de purga (retenção 90 dias) |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | RESTRICT |
| `automacao_id` | `automacoes.id` | SET NULL |
| `sessao_id` | `sessoes.id` | SET NULL |
| `cliente_id` | `clientes.id` | SET NULL |

**Regras de negócio:**
- Antes de disparar uma automação, o sistema verifica se já existe um `log_automacao` com `sessao_id + automacao_id + status IN ('pendente', 'enviado')`. Se existir, não dispara novamente.
- Registros com mais de 90 dias são excluídos definitivamente por job semanal.

---

## 12. Tabelas — Página Pública e Agendamento

### 12.1 `paginas_publicas`

**Propósito:** Página de perfil público do profissional, acessível via URL `/p/{slug}`. É o canal de captação de novos agendamentos online.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` (relação 1:1) |
| `slug` | VARCHAR(100) | Não | — | URL pública da página (ex: `ana-souza`). Escolhida pelo profissional, independente de qualquer outro identificador |
| `titulo` | VARCHAR(255) | Sim | NULL | Título da página exibido no cabeçalho |
| `bio_publica` | TEXT | Sim | NULL | Texto de apresentação visível ao cliente |
| `foto_url` | VARCHAR(500) | Sim | NULL | URL da foto de perfil pública |
| `secoes_visiveis` | JSON | Sim | NULL | Controle de quais seções aparecem na página (ver schema em §17) |
| `tags` | JSON | Sim | NULL | Lista de tags de especialidade visível ao cliente (ver schema em §17) |
| `publicada` | TINYINT(1) | Não | 0 | Quando 0, a página não é acessível publicamente |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_paginas_profissional` | `(profissional_id)` | UNIQUE | Relação 1:1 com profissional |
| `idx_paginas_slug` | `(slug)` | UNIQUE | Roteamento público `/p/{slug}` |
| `idx_paginas_publicadas` | `(publicada)` | INDEX | Filtro de páginas ativas |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | CASCADE |

> 📌 O `slug` da `paginas_publicas` é **completamente independente** de qualquer outro identificador do sistema. Um profissional com `id = 42` pode ter o slug `maria-paula` em sua página pública. O slug pode ser alterado pelo profissional, mas deve permanecer único na tabela.

---

### 12.2 `solicitacoes_agendamento`

**Propósito:** Pedidos de agendamento feitos por clientes pela página pública. Ficam pendentes até aprovação ou rejeição pelo profissional (ou membro da equipe).

**Nível de sensibilidade:** Sensível (PII — dados de contato do solicitante)

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `pagina_publica_id` | BIGINT UNSIGNED | Não | — | FK → `paginas_publicas.id` |
| `profissional_id` | BIGINT UNSIGNED | Não | — | FK → `profissionais.id` (desnormalizado) |
| `servico_id` | BIGINT UNSIGNED | Sim | NULL | FK → `servicos.id` |
| `nome` | VARCHAR(150) | Não | — | Nome do solicitante |
| `whatsapp` | VARCHAR(20) | Não | — | WhatsApp do solicitante (formato E.164) |
| `email` | VARCHAR(255) | Sim | NULL | E-mail do solicitante |
| `horario_solicitado` | DATETIME | Não | — | Data e hora desejadas |
| `mensagem` | TEXT | Sim | NULL | Mensagem opcional do solicitante |
| `status` | ENUM | Não | `'pendente'` | Estado da solicitação |
| `expira_em` | TIMESTAMP | Não | — | Data limite para resposta (padrão: 48h após criação) |
| `sessao_id` | BIGINT UNSIGNED | Sim | NULL | FK → `sessoes.id` (preenchido ao aprovar) |
| `ip_origem` | VARCHAR(45) | Sim | NULL | IP do formulário (IPv4 ou IPv6) |
| `lgpd_aceito` | TINYINT(1) | Não | 0 | Aceite dos termos no formulário público |
| `respondido_em` | TIMESTAMP | Sim | NULL | Data da resposta (aprovação ou rejeição) |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |
| `atualizado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | |

**Valores do ENUM `status`:**

```
'pendente'   → Aguardando resposta do profissional
'aprovado'   → Profissional aprovou e atendimento foi criado
'rejeitado'  → Profissional recusou o horário solicitado
'expirado'   → Passou o prazo sem resposta
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_solicitacoes_profissional` | `(profissional_id, status, criado_em)` | INDEX | Fila de aprovação no painel |
| `idx_solicitacoes_expiracao` | `(status, expira_em)` | INDEX | Job de expiração automática |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `pagina_publica_id` | `paginas_publicas.id` | RESTRICT |
| `profissional_id` | `profissionais.id` | RESTRICT |
| `servico_id` | `servicos.id` | SET NULL |
| `sessao_id` | `sessoes.id` | SET NULL |

**Regras de negócio:**
- `lgpd_aceito = 1` é validado antes de salvar o registro. O sistema não aceita solicitações sem aceite dos termos.
- Ao aprovar, o sistema cria um registro em `sessoes` e preenche `sessao_id`. Opcionalmente, cria o cliente em `clientes` se o WhatsApp não existir na base do profissional.
- Solicitações com `status = 'pendente'` e `expira_em < NOW()` são marcadas como `'expirado'` por job diário.

---

## 13. Tabelas — Auditoria

### 13.1 `logs_auditoria`

**Propósito:** Rastreabilidade de ações sensíveis realizadas no sistema. Usado para investigação de incidentes, suporte e conformidade com a LGPD.

**Nível de sensibilidade:** Normal

| Coluna | Tipo | Nulo | Padrão | Descrição |
|---|---|---|---|---|
| `id` | BIGINT UNSIGNED | Não | AUTO_INCREMENT | Chave primária |
| `profissional_id` | BIGINT UNSIGNED | Sim | NULL | FK → `profissionais.id` (NULL para ações de sistema) |
| `usuario_id` | BIGINT UNSIGNED | Sim | NULL | FK → `usuarios.id` (NULL para jobs automáticos) |
| `tipo_entidade` | VARCHAR(100) | Não | — | Nome da tabela afetada (ex: `'clientes'`, `'sessoes'`) |
| `id_entidade` | BIGINT UNSIGNED | Não | — | ID do registro afetado |
| `acao` | ENUM | Não | — | Tipo de ação realizada |
| `dados_anteriores` | JSON | Sim | NULL | Estado do registro antes da alteração — preenchido nas ações `atualizou` e `excluiu` (exceto campos de autenticação) |
| `dados_novos` | JSON | Sim | NULL | Estado do registro após a alteração — preenchido nas ações `criou` e `atualizou` |
| `ip` | VARCHAR(45) | Sim | NULL | IP do usuário no momento da ação |
| `criado_em` | TIMESTAMP | Não | CURRENT_TIMESTAMP | Data e hora imutável |

**Valores do ENUM `acao`:**

```
'criou'        → Criação de um novo registro
'atualizou'    → Edição de um registro existente
'excluiu'      → Exclusão lógica (soft delete)
'restaurou'    → Reversão de soft delete
'visualizou'   → Acesso a dado sensível (ex: dados de contato do cliente)
'login'        → Acesso autenticado ao sistema
'login_falhou' → Tentativa de login inválida
```

**Índices:**

| Nome | Colunas | Tipo | Objetivo |
|---|---|---|---|
| `PRIMARY` | `(id)` | PRIMARY | |
| `idx_auditoria_profissional` | `(profissional_id, criado_em)` | INDEX | Logs por profissional |
| `idx_auditoria_entidade` | `(tipo_entidade, id_entidade, criado_em)` | INDEX | Histórico de um registro específico |
| `idx_auditoria_purge` | `(criado_em)` | INDEX | Job de purga (retenção 1 ano) |

**Chaves estrangeiras:**

| Coluna | Referência | ON DELETE |
|---|---|---|
| `profissional_id` | `profissionais.id` | SET NULL |
| `usuario_id` | `usuarios.id` | SET NULL |

> 📌 `logs_auditoria` não possui `atualizado_em`. Os logs são imutáveis. Nunca devem ser editados, apenas consultados ou excluídos em bloco pelo job de retenção.

> ⚠️ Os campos `dados_anteriores` e `dados_novos` **nunca** devem conter `senha`, `remember_token` ou qualquer dado de autenticação. O observer responsável por preencher esses campos deve ter uma lista explícita de campos excluídos (`$hidden` do modelo Eloquent é um bom ponto de partida).

---

## 14. Estratégia de Indexação

### Princípios gerais

1. Todo índice tem um propósito documentado — índice sem objetivo de query é removido.
2. Colunas de soft delete (`excluido_em`) são incluídas em índices compostos das queries mais frequentes para evitar filtragem adicional.
3. ENUMs de baixa cardinalidade (`status`, `ativo`) só entram em índices compostos, nunca isolados.
4. Índices de `_purge` existem exclusivamente para os jobs de exclusão definitiva — não são usados em queries de produto.

### Índices críticos por frequência de uso

| Índice | Tabela | Frequência | Motivo |
|---|---|---|---|
| `idx_sessoes_agenda_dia` | `sessoes` | Altíssima | Renderização da agenda do dia — executado a cada navegação |
| `idx_sessoes_dashboard` | `sessoes` | Alta | Cards de resumo carregados no login |
| `idx_clientes_profissional_nome` | `clientes` | Alta | Busca ao digitar nome no campo de cliente |
| `idx_logs_automacao_sessao` | `logs_automacao` | Alta | Verificação de duplicata antes de cada disparo |
| `idx_sessoes_sobreposicao` | `sessoes` | Alta | Consultado pela trigger a cada inserção/atualização |
| `idx_transacoes_profissional_periodo` | `transacoes` | Média | Relatório financeiro mensal |
| `idx_solicitacoes_expiracao` | `solicitacoes_agendamento` | Baixa | Job diário de expiração |

### Estratégia de crescimento

| Fase | Threshold aproximado | Ação |
|---|---|---|
| MVP | Até 100k registros em `sessoes` | Índices simples e compostos definidos neste documento |
| Crescimento | 100k–500k registros | Avaliar read replica para queries de relatório |
| Escala | Acima de 500k registros | Particionamento por `RANGE(YEAR(inicio_em))` em `sessoes` e `transacoes` |

---

## 15. Proteção contra Sobreposição de Horários

A proteção contra double-booking opera em duas camadas complementares.

### Camada 1 — Validação na aplicação

O serviço `VerificadorDeDisponibilidade` executa, antes de salvar qualquer atendimento, uma verificação que confirma se o profissional já tem um atendimento ativo que se sobreponha ao intervalo `inicio_em`/`fim_em` solicitado:

- Condição de sobreposição: `inicio_existente < fim_novo AND fim_existente > inicio_novo`
- Filtra apenas sessões com `status NOT IN ('cancelado')` e `excluido_em IS NULL`

### Camada 2 — Trigger no banco de dados

A trigger `impedir_sobreposicao_sessao` é executada **BEFORE INSERT** e **BEFORE UPDATE** na tabela `sessoes`. Se encontrar sobreposição, interrompe a operação com erro:

- Código: `SQLSTATE 45000`
- Mensagem: `"Conflito de horário: já existe um atendimento neste período para este profissional"`

Essa segunda camada garante a proteção mesmo que a validação da aplicação seja contornada (jobs assíncronos, race condition entre requisições simultâneas).

### Índice dedicado para a trigger

O índice `idx_sessoes_sobreposicao (profissional_id, inicio_em, fim_em, status, excluido_em)` garante que a verificação da trigger nunca faça full scan, mantendo o custo da proteção em O(log n) independentemente do volume da tabela.

---

## 16. Regras de Negócio Consolidadas

### Profissional e equipe

- Um usuário pode ser **titular** de no máximo um profissional.
- Um usuário pode ser **membro de equipe** de múltiplos profissionais.
- O número de membros ativos nunca pode ultrapassar `planos.limite_assentos - 1` (o -1 é o próprio profissional).
- Ao desativar a assinatura, todos os membros da equipe perdem acesso imediatamente.

### Clientes

- Um cliente está sempre vinculado a um único profissional (`profissional_id`). Não há clientes compartilhados.
- O campo `observacoes` de clientes e atendimentos é operacional — destinado a informações de logística (endereço, link, preferência de horário). Não deve conter informações pessoais sensíveis.
- A exclusão de um cliente é sempre lógica. Clientes com sessões ou transações vinculadas não podem ser excluídos definitivamente enquanto esses registros existirem.

### Agenda

- Todo atendimento criado passa pela verificação de sobreposição (camada de aplicação + trigger).
- Atendimentos recorrentes são gerados com até 60 dias de antecedência pelo job `GerarSessoesDaRecorrencia`.
- Alterar ou cancelar uma recorrência não afeta sessões já geradas.
- Um bloqueio de agenda tem precedência sobre qualquer atendimento — o `SlotCalculator` exclui períodos bloqueados antes de calcular disponibilidade.

### Financeiro

- `transacoes` é append-only. Nenhuma transação é editada após criação.
- Estornos criam uma nova transação do tipo `'estorno'` com `origem_id` apontando para a transação original.
- O `status_pagamento` de um atendimento é atualizado automaticamente quando uma transação com `status = 'confirmado'` é registrada para aquele atendimento.

### Automações

- Nenhuma mensagem é enviada a clientes sem `consentimentos_cliente.concedido = 1` para o tipo `'receber_whatsapp'`.
- O sistema verifica duplicata em `logs_automacao` antes de cada disparo.
- Ao desconectar o WhatsApp, automações ficam em fila com `status = 'pendente'` e são processadas após reconexão.

### Página pública

- O `slug` da página pública é independente de qualquer outro identificador do sistema.
- Páginas com `publicada = 0` retornam 404 para visitantes anônimos.
- O formulário de agendamento online exige `lgpd_aceito = 1` antes de criar a solicitação.

---

## 17. Schemas JSON dos Campos Complexos

### `planos.recursos`

Controla quais funcionalidades estão habilitadas no plano.

```json
{
  "agendamento_online": true,
  "automacoes_whatsapp": true,
  "multiplas_salas": false,
  "relatorios_avancados": false,
  "exportacao_csv": true,
  "suporte_prioritario": false
}
```

| Chave | Tipo | Descrição |
|---|---|---|
| `agendamento_online` | boolean | Página pública e formulário de agendamento disponíveis |
| `automacoes_whatsapp` | boolean | Módulo de automações habilitado |
| `multiplas_salas` | boolean | Cadastro de mais de uma sala/local |
| `relatorios_avancados` | boolean | Relatórios com filtros avançados e gráficos |
| `exportacao_csv` | boolean | Exportação de dados em CSV |
| `suporte_prioritario` | boolean | Canal de suporte prioritário ativo |

---

### `paginas_publicas.secoes_visiveis`

Controla quais blocos aparecem na página pública do profissional.

```json
{
  "sobre": true,
  "servicos": true,
  "disponibilidade": true,
  "localizacao": false,
  "tags": true
}
```

| Chave | Tipo | Descrição |
|---|---|---|
| `sobre` | boolean | Exibe a bio pública do profissional |
| `servicos` | boolean | Exibe os serviços com duração e valor |
| `disponibilidade` | boolean | Exibe calendário de horários disponíveis |
| `localizacao` | boolean | Exibe endereço ou localização |
| `tags` | boolean | Exibe as tags de especialidade |

---

### `paginas_publicas.tags`

Lista de tags de especialidade ou área de atuação exibidas na página pública.

```json
["Presencial", "Online", "Individual", "Consultoria"]
```

- Array de strings.
- Cada tag tem no máximo 40 caracteres.
- Máximo de 10 tags por página.
- Preenchido pelo profissional via editor da página.

---

## 18. Política de Retenção de Dados

| Dado | Retenção | Após o prazo |
|---|---|---|
| Dados do profissional e clientes | Enquanto a conta estiver ativa | Excluídos definitivamente 30 dias após cancelamento |
| Atendimentos e histórico financeiro | Enquanto a conta estiver ativa | Excluídos definitivamente 30 dias após cancelamento |
| Solicitações de agendamento aprovadas | Conforme o atendimento vinculado | — |
| Solicitações de agendamento expiradas/rejeitadas | 90 dias após `criado_em` | Excluídas definitivamente por job semanal |
| Logs de automação | 90 dias após `criado_em` | Excluídos definitivamente por job semanal |
| Logs de auditoria | 1 ano após `criado_em` | Excluídos definitivamente por job mensal |
| Convites não aceitos | 7 dias após `criado_em` | Excluídos definitivamente por job semanal |
| Tokens de redefinição de senha | 60 minutos após `criado_em` | Excluídos automaticamente pelo framework |

### Fluxo de cancelamento de conta

```
1. Profissional cancela assinatura
2. Assinatura → status = 'cancelada', termina_em = data do cancelamento
3. Profissional tem 30 dias para exportar seus dados (período de carência)
4. Após 30 dias: job exclui definitivamente todos os registros do profissional_id
   na seguinte ordem (respeitando FKs):
   logs_auditoria → logs_automacao → solicitacoes_agendamento →
   transacoes → sessoes → consentimentos_cliente → clientes →
   automacoes → conexoes_whatsapp → paginas_publicas →
   politicas_cancelamento → recorrencias → bloqueios_agenda →
   horarios_atendimento → salas → servicos → convites →
   membros_equipe → faturas → assinaturas → profissionais → usuarios
```

---

## 19. Ordem de Criação das Tabelas

A ordem abaixo respeita todas as dependências de chaves estrangeiras:

```
 1. usuarios
 2. tokens_redefinicao_senha
 3. planos
 4. profissionais               (depende de: usuarios)
 5. assinaturas                 (depende de: profissionais, planos)
 6. faturas                     (depende de: assinaturas, profissionais)
 7. membros_equipe              (depende de: profissionais, usuarios)
 8. convites                    (depende de: profissionais)
 9. servicos                    (depende de: profissionais)
10. salas                       (depende de: profissionais)
11. horarios_atendimento        (depende de: profissionais)
12. bloqueios_agenda            (depende de: profissionais)
13. politicas_cancelamento      (depende de: profissionais)
14. clientes                    (depende de: profissionais)
15. consentimentos_cliente      (depende de: clientes, profissionais)
16. recorrencias                (depende de: profissionais, clientes, servicos, salas, politicas_cancelamento)
17. sessoes                     (depende de: profissionais, clientes, servicos, salas, recorrencias, usuarios, politicas_cancelamento)
18. conexoes_whatsapp           (depende de: profissionais)
19. automacoes                  (depende de: profissionais)
20. logs_automacao              (depende de: profissionais, automacoes, sessoes, clientes)
21. transacoes                  (depende de: profissionais, sessoes, clientes, usuarios, logs_automacao)
22. paginas_publicas            (depende de: profissionais)
23. solicitacoes_agendamento    (depende de: paginas_publicas, profissionais, servicos, sessoes)
24. logs_auditoria              (depende de: profissionais, usuarios)

--- Infraestrutura Laravel (sem dependências de domínio) ---
25. http_sessions               (renomeada via config/session.php — evita conflito com tabela de domínio `sessoes`)
26. cache
27. cache_locks
28. filas_trabalho
29. lotes_trabalho
30. trabalhos_falhados
```

---

## 20. Glossário

| Termo | Definição |
|---|---|
| **Assento** | Vaga de usuário em uma conta. O plano define quantos usuários (profissional + membros de equipe) podem operar simultaneamente. |
| **Soft delete** | Exclusão lógica via campo `excluido_em`. O registro permanece no banco mas é invisível às queries normais. |
| **Append-only** | Tabela na qual registros nunca são editados após inserção. Alterações são representadas por novos registros. |
| **Estorno** | Transação do tipo `'estorno'` que referencia uma transação original via `origem_id`. Nunca cancela a transação original. |
| **Slot** | Intervalo de tempo disponível para agendamento, calculado a partir dos horários de atendimento menos os atendimentos já existentes e bloqueios. |
| **SlotCalculator** | Componente da aplicação responsável por calcular os slots disponíveis cruzando `horarios_atendimento`, `sessoes` e `bloqueios_agenda`. |
| **Recorrência** | Regra de repetição de atendimentos com frequência fixa (semanal, quinzenal, mensal). Gera atendimentos individualmente de forma automática. |
| **Double-booking** | Situação em que dois atendimentos são agendados no mesmo horário para o mesmo profissional. Prevenida por trigger e validação na aplicação. |
| **Global Scope** | Filtro automático do Eloquent que injeta `profissional_id` em todas as queries de tabelas de domínio, garantindo isolamento de dados entre profissionais. |
| **Tenant** | O profissional autônomo — unidade de isolamento de dados no Sabenta. Cada profissional opera em seu próprio espaço de dados independente. |
| **E.164** | Formato internacional de número de telefone (ex: `+5511999999999`). Usado para garantir compatibilidade com APIs de WhatsApp. |
| **PII** | Informação de identificação pessoal (nome, e-mail, telefone). Dados sujeitos à LGPD. |
| **LGPD** | Lei Geral de Proteção de Dados (Lei 13.709/2018). Define regras para coleta e uso de dados pessoais no Brasil. |
| **Feature flag** | Chave booleana no campo JSON `recursos` de `planos` que habilita ou desabilita uma funcionalidade para um plano específico. |
