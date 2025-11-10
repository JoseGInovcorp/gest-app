# Módulo de Contas Bancárias

## Visão Geral

O módulo de Contas Bancárias permite a gestão completa das contas bancárias da empresa, incluindo IBAN, banco, saldos e movimentos associados.

## Data de Implementação

-   **Criado em**: 10 de Novembro de 2025
-   **Versão**: 1.0

## Estrutura da Base de Dados

### Tabela: `bank_accounts`

```sql
- id (bigint, PK, auto_increment)
- nome (varchar 255) - Nome da conta
- banco (varchar 255) - Nome do banco
- iban (varchar 255, unique) - IBAN da conta
- swift_bic (varchar 11, nullable) - Código SWIFT/BIC
- saldo_inicial (decimal 10,2) - Saldo inicial da conta
- saldo_atual (decimal 10,2) - Saldo atual (calculado automaticamente)
- moeda (varchar 3, default 'EUR') - Moeda da conta
- tipo (enum: corrente, poupanca, credito, investimento) - Tipo de conta
- estado (enum: ativa, inativa, encerrada) - Estado da conta
- observacoes (text, nullable) - Observações adicionais
- created_at, updated_at (timestamps)
- deleted_at (timestamp, nullable) - Soft delete
```

**Índices:**

-   `iban` - Unique
-   `tipo`
-   `estado`

### Tabela: `bank_transactions`

```sql
- id (bigint, PK, auto_increment)
- bank_account_id (bigint, FK -> bank_accounts.id, cascade)
- data_movimento (date) - Data do movimento
- descricao (varchar 255) - Descrição do movimento
- tipo (enum: credito, debito) - Tipo de movimento
- valor (decimal 10,2) - Valor do movimento
- saldo_apos (decimal 10,2) - Saldo após o movimento
- referencia (varchar 255, nullable) - Referência do movimento
- categoria (enum) - Categoria do movimento:
  * transferencia_recebida
  * transferencia_enviada
  * pagamento_fornecedor
  * recebimento_cliente
  * levantamento
  * deposito
  * juros
  * comissoes
- observacoes (text, nullable)
- created_at, updated_at (timestamps)
```

**Índices:**

-   `bank_account_id, data_movimento`
-   `tipo`

## Models

### BankAccount.php

**Localização**: `app/Models/BankAccount.php`

**Funcionalidades:**

-   Soft Deletes habilitado
-   Cálculo automático de `saldo_atual` na criação
-   Método `updateBalance()` para recalcular saldo a partir dos movimentos
-   Accessor `getIbanFormatadoAttribute()` para formatar IBAN

**Relacionamentos:**

-   `hasMany(BankTransaction)` - Relacionamento com movimentos

**Boot Events:**

-   `creating`: Define `saldo_atual = saldo_inicial` automaticamente

**Métodos Principais:**

```php
updateBalance() // Recalcula saldo_atual com base nos movimentos
getIbanFormatadoAttribute() // Retorna IBAN formatado em blocos de 4
```

### BankTransaction.php

**Localização**: `app/Models/BankTransaction.php`

**Funcionalidades:**

-   Atualização automática do saldo da conta em eventos created/updated/deleted
-   Relacionamento com BankAccount

**Boot Events:**

-   `created`: Chama `bankAccount->updateBalance()`
-   `updated`: Chama `bankAccount->updateBalance()`
-   `deleted`: Chama `bankAccount->updateBalance()`

## Controller

### BankAccountController.php

**Localização**: `app/Http/Controllers/BankAccountController.php`

**Rotas:**

-   `GET /bank-accounts` - Lista todas as contas (com paginação 15)
-   `GET /bank-accounts/create` - Formulário de criação
-   `POST /bank-accounts` - Criar nova conta
-   `GET /bank-accounts/{id}` - Ver detalhes
-   `GET /bank-accounts/{id}/edit` - Formulário de edição
-   `PATCH /bank-accounts/{id}` - Atualizar conta
-   `DELETE /bank-accounts/{id}` - Eliminar conta (soft delete)

**Funcionalidades:**

#### Index

-   Paginação: 15 registos por página
-   Filtros:
    -   `search`: Nome, banco ou IBAN
    -   `estado`: ativa, inativa, encerrada
    -   `tipo`: corrente, poupanca, credito, investimento
-   Retorna: Lista com `transactions_count` por conta

#### Store

-   Validação:
    -   `nome`: required, string, max:255
    -   `banco`: required, string, max:255
    -   `iban`: required, string, unique
    -   `swift_bic`: nullable, string, max:11
    -   `saldo_inicial`: required, numeric
    -   `moeda`: required, string, size:3
    -   `tipo`: required, enum
    -   `estado`: required, enum
    -   `observacoes`: nullable, string

#### Update

-   Recalcula `saldo_atual` quando `saldo_inicial` é alterado
-   Mesma validação do store (exceto unique no IBAN se não mudou)

#### Show

-   Carrega movimentos ordenados por `data_movimento desc`
-   Eager loading de transações

## Permissões

### Permissions Criadas

-   `bank-accounts.create`
-   `bank-accounts.read`
-   `bank-accounts.update`
-   `bank-accounts.delete`

### Atribuição de Roles

-   **Super Admin**: Todas as permissões
-   **Gestor Financeiro**: Todas as permissões
-   **Visualizador**: Apenas `bank-accounts.read`

**Seeder**: `BankAccountPermissionsSeeder.php`

## Interface Frontend

### Páginas

#### Index.vue

**Localização**: `resources/js/Pages/BankAccounts/Index.vue`

**Componentes:**

-   Header com título e descrição
-   Breadcrumbs de navegação
-   Toolbar com:
    -   Pesquisa (nome, banco, IBAN)
    -   Filtro por tipo
    -   Filtro por estado
    -   Botão "Nova Conta"
-   Tabela com colunas:
    -   Nome
    -   Banco
    -   IBAN (formatado)
    -   Tipo
    -   Saldo Atual (colorido: verde=positivo, vermelho=negativo)
    -   Estado (badge colorido)
    -   Nº Movimentos
    -   Ações (Ver, Editar, Eliminar)
-   Paginação

**Badges de Estado:**

-   Ativa: Verde
-   Inativa: Laranja
-   Encerrada: Vermelho

#### Create.vue

**Localização**: `resources/js/Pages/BankAccounts/Create.vue`

**Formulário:**

-   Informações Básicas:
    -   Nome da Conta \*
    -   Banco \*
    -   IBAN \*
    -   SWIFT/BIC
-   Detalhes Financeiros:
    -   Saldo Inicial \*
    -   Moeda (EUR, USD, GBP) \*
    -   Tipo de Conta (Corrente, Poupança, Crédito, Investimento) \*
-   Status e Observações:
    -   Estado (Ativa, Inativa, Encerrada) \*
    -   Observações

**Validação**: Formulário com validação em tempo real

#### Edit.vue

**Localização**: `resources/js/Pages/BankAccounts/Edit.vue`

**Funcionalidades:**

-   Mesmo formulário que Create.vue
-   Pré-preenchido com dados existentes
-   Botão "Atualizar Conta"

#### Show.vue

**Localização**: `resources/js/Pages/BankAccounts/Show.vue`

**Funcionalidades:**

-   Visualização completa dos detalhes da conta
-   Lista de movimentos associados
-   Botões de ação (Editar, Eliminar)

## Navegação

### Menu Lateral

-   **Localização**: Financeiro > Contas Bancárias
-   **Ícone**: CreditCard
-   **Permissão**: `bank-accounts.*`
-   **Arquivo**: `resources/js/Layouts/AuthenticatedLayout.vue`

## Migrations

### 2025_11_10_125754_create_bank_accounts_table.php

Cria a tabela `bank_accounts` com todos os campos e índices.

### 2025_11_10_130104_create_bank_transactions_table.php

Cria a tabela `bank_transactions` com relacionamento para `bank_accounts`.

## Lógica de Negócio

### Cálculo de Saldo

1. `saldo_inicial` é definido na criação da conta
2. `saldo_atual` começa igual ao `saldo_inicial`
3. Cada `BankTransaction` atualiza o `saldo_atual`:
    - **Crédito**: `saldo_atual += valor`
    - **Débito**: `saldo_atual -= valor`
4. Método `updateBalance()` recalcula somando todos os créditos e subtraindo todos os débitos ao saldo inicial

### Soft Deletes

-   Contas eliminadas não são removidas fisicamente
-   Campo `deleted_at` é preenchido
-   Permite restauração futura se necessário

## Exemplos de Uso

### Criar uma Conta Bancária

1. Navegar para Financeiro > Contas Bancárias
2. Clicar em "Nova Conta"
3. Preencher formulário
4. Guardar

### Filtrar Contas

-   Por pesquisa: Digitar nome, banco ou IBAN
-   Por tipo: Selecionar dropdown "Tipo"
-   Por estado: Selecionar dropdown "Estado"

### Ver Movimentos

1. Clicar no ícone "olho" de uma conta
2. Ver lista completa de movimentos

## Melhorias Futuras

-   [ ] Exportar para Excel/PDF
-   [ ] Gráficos de evolução de saldo
-   [ ] Reconciliação bancária
-   [ ] Importação de extratos bancários
-   [ ] Alertas de saldo baixo
-   [ ] Dashboard com resumo financeiro

## Notas Técnicas

### Performance

-   Índices criados em campos frequentemente consultados
-   Eager loading de relacionamentos para evitar N+1
-   Paginação implementada em todas as listagens

### Segurança

-   Validação de IBAN único
-   Soft deletes para evitar perda de dados
-   Middleware de permissões em todas as rotas
-   CSRF protection habilitado

### Responsividade

-   Interface adaptada para desktop e mobile
-   Grid responsivo (1 coluna mobile, 2-3 desktop)
-   Tabelas com scroll horizontal em dispositivos pequenos
