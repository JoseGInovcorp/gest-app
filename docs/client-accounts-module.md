# Módulo de Conta Corrente de Clientes

## Visão Geral

O módulo de Conta Corrente de Clientes permite acompanhar débitos e créditos de cada cliente, facilitando o controle de pagamentos, faturas e saldos em aberto. Funciona como um extrato financeiro por cliente, mostrando todas as movimentações e o saldo atual.

## Data de Implementação

-   **Criado em**: 10 de Novembro de 2025
-   **Versão**: 1.0

## Conceitos Fundamentais

### Débito vs Crédito

-   **Débito**: Cliente **deve** à empresa (aumenta o saldo)
    -   Exemplo: Emissão de uma fatura
-   **Crédito**: Cliente **pagou** à empresa (diminui o saldo)
    -   Exemplo: Recebimento de pagamento

### Saldo

-   **Saldo Positivo**: Cliente tem dívida com a empresa
-   **Saldo Negativo**: Empresa deve ao cliente (crédito a favor)
-   **Saldo Zero**: Conta equilibrada

## Estrutura da Base de Dados

### Tabela: `client_accounts`

```sql
- id (bigint, PK, auto_increment)
- entity_id (bigint, FK -> entities.id, cascade) - Cliente
- data_movimento (date) - Data do movimento
- tipo (enum: debito, credito) - Tipo de movimento
- valor (decimal 10,2) - Valor do movimento
- saldo_apos (decimal 10,2, nullable) - Saldo após este movimento
- descricao (varchar 255) - Descrição do movimento
- categoria (enum) - Categoria do movimento:
  * fatura
  * pagamento
  * nota_credito
  * nota_debito
  * juros
  * ajuste
  * outros
- referencia (varchar 255, nullable) - Nº da fatura, recibo, etc
- related_id (bigint, nullable) - ID do documento relacionado
- related_type (varchar 255, nullable) - Tipo do documento (Invoice, Payment, etc)
- observacoes (text, nullable) - Observações adicionais
- created_at, updated_at (timestamps)
```

**Índices:**

-   `entity_id, data_movimento` - Para consultas rápidas por cliente e data
-   `tipo, categoria` - Para filtros

## Models

### ClientAccount.php

**Localização**: `app/Models/ClientAccount.php`

**Funcionalidades:**

-   Cálculo automático de saldos em tempo real
-   Atualização em cascata de saldos subsequentes
-   Relacionamento polimórfico com documentos
-   Scopes para filtros avançados

**Relacionamentos:**

-   `belongsTo(Entity)` - Cliente
-   `morphTo()` - Documento relacionado (polimórfico)

**Boot Events:**

```php
creating: calculateBalance() // Calcula saldo antes de criar
created: updateSubsequentBalances() // Atualiza saldos posteriores
updated: updateSubsequentBalances() // Recalcula após edição
deleted: recalculateBalancesForEntity() // Recalcula tudo após deletar
```

**Métodos Principais:**

#### calculateBalance()

Calcula o saldo após o movimento atual:

-   Busca último movimento anterior
-   Débito: `saldo_apos = saldo_anterior + valor`
-   Crédito: `saldo_apos = saldo_anterior - valor`

#### updateSubsequentBalances()

Atualiza todos os movimentos posteriores em cascata:

-   Busca movimentos após a data atual
-   Recalcula saldo de cada um sequencialmente
-   Usa `saveQuietly()` para evitar loops

#### recalculateBalancesForEntity(entityId)

Recalcula **todos** os saldos de um cliente do zero:

-   Usado quando um movimento é deletado
-   Percorre todos os movimentos em ordem cronológica
-   Garante consistência total dos dados

#### getCurrentBalance(entityId)

Retorna o saldo atual de um cliente:

-   Busca último movimento
-   Retorna `saldo_apos` ou 0 se não houver movimentos

#### getEntityStats(entityId)

Retorna estatísticas completas:

```php
[
    'total_debitos' => 1500.00,    // Soma de todos os débitos
    'total_creditos' => 1200.00,   // Soma de todos os créditos
    'saldo_atual' => 300.00        // Saldo atual
]
```

**Scopes:**

```php
forEntity($entityId)              // Filtrar por cliente
ofType($type)                     // Filtrar por débito/crédito
ofCategory($category)             // Filtrar por categoria
betweenDates($start, $end)        // Filtrar por período
```

## Controller

### ClientAccountController.php

**Localização**: `app/Http/Controllers/ClientAccountController.php`

**Rotas:**

-   `GET /client-accounts` - Lista movimentos (paginação 15)
-   `GET /client-accounts/create` - Formulário de novo movimento
-   `POST /client-accounts` - Criar movimento
-   `GET /client-accounts/{id}` - Ver detalhes
-   `GET /client-accounts/{id}/edit` - Formulário de edição
-   `PATCH /client-accounts/{id}` - Atualizar movimento
-   `DELETE /client-accounts/{id}` - Eliminar movimento

**Funcionalidades:**

#### Index

-   **Paginação**: 15 registos por página
-   **Filtros**:
    -   `entity_id`: Cliente específico
    -   `tipo`: débito ou crédito
    -   `categoria`: fatura, pagamento, etc
    -   `start_date`: Data inicial
    -   `end_date`: Data final
    -   `search`: Pesquisa em descrição ou referência
-   **Ordenação**: Por data decrescente (mais recente primeiro)
-   **Estatísticas**: Se cliente selecionado, mostra total débitos, créditos e saldo
-   **Eager Loading**: Carrega dados do cliente (`entity:id,name`)

#### Store

-   **Validação**:
    -   `entity_id`: required, exists:entities
    -   `data_movimento`: required, date
    -   `tipo`: required, in:debito,credito
    -   `valor`: required, numeric, min:0.01
    -   `descricao`: required, string, max:255
    -   `categoria`: required, enum
    -   `referencia`: nullable, string, max:255
    -   `observacoes`: nullable, string
-   **Processo**:
    1. Valida dados
    2. Cria movimento
    3. Boot event calcula saldo automaticamente
    4. Redireciona para index com mensagem de sucesso

#### Update

-   Atualiza movimento
-   Recalcula automaticamente todos os saldos subsequentes
-   Garante integridade dos dados

#### Destroy

-   Elimina movimento
-   Recalcula todos os saldos do cliente
-   Garante consistência após remoção

## Permissões

### Permissions Criadas

-   `client-accounts.create`
-   `client-accounts.read`
-   `client-accounts.update`
-   `client-accounts.delete`

### Atribuição de Roles

-   **Super Admin**: Todas as permissões
-   **Gestor Financeiro**: Todas as permissões
-   **Visualizador**: Apenas `client-accounts.read`

**Seeder**: `ClientAccountPermissionsSeeder.php`

## Interface Frontend

### Páginas

#### Index.vue

**Localização**: `resources/js/Pages/ClientAccounts/Index.vue`

**Layout Superior:**

-   Painel de Estatísticas (quando cliente selecionado):
    -   Nome do Cliente
    -   Total Débitos (vermelho)
    -   Total Créditos (verde)
    -   Saldo Atual (verde se negativo, vermelho se positivo)

**Filtros:**

-   Seleção de Cliente (dropdown)
-   Pesquisa (descrição ou referência)
-   Tipo (Todos, Débito, Crédito)
-   Categoria (8 opções)
-   Data Início
-   Data Fim
-   Botão "Novo Movimento"

**Tabela:**
| Coluna | Descrição |
|--------|-----------|
| Data | Data do movimento |
| Cliente | Nome do cliente |
| Descrição | Descrição do movimento |
| Categoria | Badge colorido por tipo |
| Referência | Nº fatura/recibo |
| Débito | Valor em vermelho (se débito) |
| Crédito | Valor em verde (se crédito) |
| Saldo | Saldo após movimento (colorido) |
| Ações | Ver, Editar, Eliminar |

**Cores dos Badges:**

-   Fatura: Vermelho
-   Pagamento: Verde
-   Nota Crédito: Azul
-   Nota Débito: Laranja
-   Juros: Roxo
-   Ajuste: Amarelo
-   Outros: Cinza

**Paginação**: Links com navegação anterior/próxima

#### Create.vue

**Localização**: `resources/js/Pages/ClientAccounts/Create.vue`

**Formulário:**

-   **Cliente** \* (dropdown)
-   **Data do Movimento** \* (date, default: hoje)
-   **Tipo** \* (dropdown)
    -   Débito (Cliente deve)
    -   Crédito (Cliente pagou)
-   **Valor (€)** \* (number, step 0.01)
-   **Categoria** \* (dropdown: 7 opções)
-   **Referência** (text, exemplo: nº fatura)
-   **Descrição** \* (text)
-   **Observações** (textarea)

**Botões:**

-   Cancelar (volta para index)
-   Registar Movimento (submit)

#### Edit.vue

**Localização**: `resources/js/Pages/ClientAccounts/Edit.vue`

**Funcionalidades:**

-   Mesmo formulário que Create.vue
-   Pré-preenchido com dados existentes
-   Botão "Atualizar Movimento"
-   Recalcula saldos automaticamente ao guardar

#### Show.vue

**Localização**: `resources/js/Pages/ClientAccounts/Show.vue`

**Layout em 2 Colunas:**

**Coluna Principal (2/3):**

-   Informações do Movimento:
    -   Cliente
    -   Data
    -   Tipo (badge)
    -   Valor (colorido)
    -   Categoria (badge)
    -   Referência
    -   Descrição
    -   Observações

**Sidebar (1/3):**

-   **Card Saldo**:
    -   Saldo Após Movimento
    -   Indicador (deve/recebeu)
-   **Card Metadata**:
    -   Criado em
    -   Última atualização
    -   ID do Movimento
-   **Card Ações**:
    -   Editar Movimento
    -   Eliminar Movimento
    -   Voltar à Lista

## Navegação

### Menu Lateral

-   **Localização**: Financeiro > Conta Corrente Clientes
-   **Ícone**: DollarSign
-   **Permissão**: `client-accounts.*`
-   **Arquivo**: `resources/js/Layouts/AuthenticatedLayout.vue`
-   **Posição**: Segundo item no submenu Financeiro

## Migrations

### 2025_11_10_132125_create_client_accounts_table.php

Cria a tabela `client_accounts` com:

-   Campos completos
-   Foreign key para `entities`
-   Índices compostos para performance
-   Enum com todas as categorias

## Lógica de Negócio

### Fluxo de Criação de Movimento

```
1. User preenche formulário
2. POST /client-accounts
3. Validação dos dados
4. ClientAccount::create()
5. Boot::creating()
   ├─ calculateBalance()
   │  ├─ Busca movimento anterior
   │  ├─ Calcula novo saldo
   │  └─ Define saldo_apos
   └─ Salva no banco
6. Boot::created()
   └─ updateSubsequentBalances()
      ├─ Busca movimentos posteriores
      ├─ Recalcula cada um
      └─ Salva silenciosamente
7. Redirect com mensagem de sucesso
```

### Fluxo de Atualização de Movimento

```
1. User edita movimento
2. PATCH /client-accounts/{id}
3. Validação dos dados
4. ClientAccount::update()
5. Boot::updated()
   └─ updateSubsequentBalances()
      ├─ Recalcula saldo do movimento atual
      ├─ Busca movimentos posteriores
      ├─ Recalcula cada um em cascata
      └─ Garante consistência
6. Redirect com mensagem de sucesso
```

### Fluxo de Eliminação de Movimento

```
1. User confirma eliminação
2. DELETE /client-accounts/{id}
3. ClientAccount::delete()
4. Boot::deleted()
   └─ recalculateBalancesForEntity()
      ├─ Busca TODOS movimentos do cliente
      ├─ Recalcula do zero (saldo inicial = 0)
      ├─ Percorre em ordem cronológica
      └─ Garante integridade total
5. Redirect com mensagem de sucesso
```

## Casos de Uso

### 1. Registar Fatura de Cliente

```
Cliente: João Silva
Data: 10/11/2025
Tipo: Débito
Valor: 500.00 €
Categoria: Fatura
Referência: FT 2025/001
Descrição: Fatura de serviços - Novembro 2025

Resultado:
- Saldo anterior: 200.00 €
- Novo saldo: 700.00 € (cliente deve mais 500€)
```

### 2. Registar Pagamento de Cliente

```
Cliente: João Silva
Data: 15/11/2025
Tipo: Crédito
Valor: 300.00 €
Categoria: Pagamento
Referência: RC 2025/045
Descrição: Pagamento parcial fatura FT 2025/001

Resultado:
- Saldo anterior: 700.00 €
- Novo saldo: 400.00 € (cliente pagou 300€, ainda deve 400€)
```

### 3. Nota de Crédito

```
Cliente: João Silva
Data: 20/11/2025
Tipo: Crédito
Valor: 50.00 €
Categoria: Nota Crédito
Referência: NC 2025/010
Descrição: Devolução de produtos

Resultado:
- Saldo anterior: 400.00 €
- Novo saldo: 350.00 € (descontou 50€)
```

## Relatórios e Consultas

### Ver Saldo de um Cliente

```php
$saldo = ClientAccount::getCurrentBalance($entityId);
// Retorna: 350.00
```

### Ver Estatísticas Completas

```php
$stats = ClientAccount::getEntityStats($entityId);
// Retorna:
// [
//     'total_debitos' => 500.00,
//     'total_creditos' => 150.00,
//     'saldo_atual' => 350.00
// ]
```

### Listar Movimentos de um Período

```php
$movimentos = ClientAccount::forEntity($entityId)
    ->betweenDates('2025-11-01', '2025-11-30')
    ->orderBy('data_movimento')
    ->get();
```

### Filtrar por Categoria

```php
$faturas = ClientAccount::forEntity($entityId)
    ->ofCategory('fatura')
    ->get();
```

## Performance

### Otimizações Implementadas

-   **Índices compostos**: `(entity_id, data_movimento)` para queries rápidas
-   **Eager Loading**: Carrega cliente junto com movimentos (evita N+1)
-   **Paginação**: Limita a 15 registos por página
-   **saveQuietly()**: Evita loops infinitos em recálculos

### Queries Otimizadas

```php
// ❌ Ruim - N+1 queries
$movements = ClientAccount::all();
foreach ($movements as $movement) {
    echo $movement->entity->name; // Query para cada movimento
}

// ✅ Bom - 2 queries apenas
$movements = ClientAccount::with('entity')->paginate(15);
foreach ($movements as $movement) {
    echo $movement->entity->name; // Dados já carregados
}
```

## Segurança

### Validações

-   Entity existe na tabela `entities`
-   Valor mínimo: 0.01 €
-   Tipos e categorias restritos a enums
-   CSRF protection em todos os forms

### Integridade de Dados

-   Foreign key com cascade delete
-   Recálculo automático de saldos
-   Transações implícitas no Eloquent

## Testes Manuais

### Checklist de Testes

-   [ ] Criar movimento de débito
-   [ ] Criar movimento de crédito
-   [ ] Verificar saldo calculado corretamente
-   [ ] Editar movimento antigo e verificar recálculo
-   [ ] Eliminar movimento e verificar recálculo
-   [ ] Filtrar por cliente
-   [ ] Filtrar por período
-   [ ] Filtrar por categoria
-   [ ] Pesquisar por descrição
-   [ ] Paginação funcionando
-   [ ] Estatísticas corretas

## Melhorias Futuras

### Próximas Versões

-   [ ] Exportar extrato para PDF
-   [ ] Enviar extrato por email ao cliente
-   [ ] Gráfico de evolução de saldo
-   [ ] Dashboard com clientes em atraso
-   [ ] Alertas automáticos de pagamentos vencidos
-   [ ] Integração com módulo de Faturas (quando criado)
-   [ ] Reconciliação automática com pagamentos bancários
-   [ ] Previsão de recebimentos
-   [ ] Relatório de idade de saldos (aging)

### Integrações Planejadas

-   **Faturas**: Criar movimento automático ao emitir fatura
-   **Pagamentos**: Criar movimento automático ao receber pagamento
-   **Contas Bancárias**: Ligar movimento com extrato bancário

## Troubleshooting

### Saldos Descalibrados

**Problema**: Saldos não batem com a realidade

**Solução**:

```php
// Recalcular todos os saldos de um cliente
ClientAccount::recalculateBalancesForEntity($entityId);
```

### Performance Lenta

**Problema**: Listagem demora muito

**Verificar**:

1. Índices criados? `SHOW INDEX FROM client_accounts`
2. Eager loading ativado? `with('entity')`
3. Paginação funcionando? `paginate(15)`

### Movimentos Duplicados

**Problema**: Mesmo movimento aparece 2x

**Causa**: Possível click duplo no botão submit

**Prevenção**: Disable button durante `form.processing`

## Notas Técnicas

### Diferença para Contabilidade

Este módulo é um **extrato simplificado**, não substitui um sistema contábil completo:

-   Não usa partidas dobradas
-   Não tem plano de contas
-   Foco em contas a receber de clientes
-   Para contabilidade completa, integrar com software especializado

### Arquitetura

-   **MVC**: Separação clara de responsabilidades
-   **Eloquent ORM**: Models com lógica de negócio
-   **Inertia.js**: SPA sem API
-   **Vue 3**: Composição API e reatividade
-   **TailwindCSS**: Estilização utilitária

### Padrões Utilizados

-   **Repository Pattern**: Métodos estáticos no Model
-   **Observer Pattern**: Boot events do Eloquent
-   **Strategy Pattern**: Cálculo de saldo por tipo
-   **Factory Pattern**: FormRequest validation
