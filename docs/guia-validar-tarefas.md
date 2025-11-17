# 📋 Guia: Como Validar e Gerir Tarefas

**Versão:** 1.0  
**Data:** 17 Nov 2025  
**Módulo:** Ordens de Trabalho

---

## 🎯 Visão Geral

O sistema de Ordens de Trabalho permite gerir tarefas relacionadas com o processamento de encomendas. As tarefas são **criadas automaticamente** quando uma encomenda é feita e seguem um **fluxo sequencial**.

### Acesso Rápido

🌐 **URL:** http://gest-app.test  
📍 **Menu:** Ordens de Trabalho → Minhas Tarefas  
📍 **Menu:** Ordens de Trabalho → Todas as Ordens

---

## 🚀 Método 1: Dashboard Pessoal (Recomendado)

### Passo a Passo

1. **Login** no sistema (http://gest-app.test)
2. No menu lateral, clica em **"Ordens de Trabalho"**
3. Seleciona **"Minhas Tarefas"**

### O que vês

📊 **Lista das tuas tarefas:**

-   Tarefas atribuídas diretamente a ti
-   Tarefas atribuídas ao teu grupo/papel (ex: Gestor Comercial)
-   Ordenadas por: status (em progresso → pendente) e prazo

### Informação Mostrada

Cada tarefa mostra:

-   ✅ **Título** da tarefa
-   📦 **Ordem de Trabalho** associada
-   👤 **Cliente** da encomenda
-   📅 **Prazo** (due date)
-   ⚠️ **Indicador de atraso** (se ultrapassou o prazo)
-   🔒 **Bloqueio** (se depende de outra tarefa)

### Ações Disponíveis

#### A. Iniciar Tarefa (botão verde "Iniciar")

**Quando:** Tarefa está **pendente** e **desbloqueada**

**O que faz:**

-   Muda status para "em_progresso"
-   Atribui-te automaticamente (se não estava atribuída)
-   Atualiza status da Ordem de Trabalho para "em_progresso"

**Exemplo:**

```
[PENDENTE] Validar Disponibilidade em Armazém
Ordem: Processar Encomenda ORD-00026
Cliente: João Ninguém
Prazo: 18/11/2025

[Botão: Iniciar] [Link: Ver Ordem]
```

Clicar "Iniciar" → Tarefa fica:

```
[EM PROGRESSO] Validar Disponibilidade em Armazém
Atribuída a: Super Administrator (tu)
```

#### B. Concluir Tarefa (botão azul "Concluir")

**Quando:** Tarefa está **em_progresso**

**O que faz:**

1. Abre prompt para **observações** (opcional)
2. Marca tarefa como **concluída**
3. Regista data/hora de conclusão
4. Guarda as tuas observações
5. **Desbloqueia a próxima tarefa** (se houver)
6. Atualiza progresso da Ordem de Trabalho

**Exemplo:**

```
[EM PROGRESSO] Validar Disponibilidade em Armazém
Atribuída a: Super Administrator

[Botão: Concluir] [Link: Ver Ordem]
```

Clicar "Concluir" → Prompt aparece:

```
Observações sobre a conclusão da tarefa (opcional):
_________________________________
[OK] [Cancelar]
```

Escreves: "Stock validado. 50 unidades disponíveis."

Resultado:

-   ✅ Tarefa marcada como concluída
-   ✅ Nota guardada: "Stock validado. 50 unidades disponíveis."
-   ✅ Próxima tarefa desbloqueada automaticamente

---

## 🗺️ Método 2: Timeline Visual (Ver Ordem Completa)

### Passo a Passo

1. **Menu:** Ordens de Trabalho → Todas as Ordens
2. Clica em **"Ver Detalhes"** numa ordem
3. Vês a **timeline completa** de tarefas

### O que vês

📊 **Timeline Visual:**

```
================================================================================
Tarefas (9)
================================================================================

┌─────────────────────────────────────────────────────────────────────┐
│ ✅  1. Validar Disponibilidade em Armazém                          │
│     [CONCLUÍDA]                                                     │
│     👤 Super Administrator | 📅 18/11/2025                          │
│     📝 Stock validado. 50 unidades disponíveis.                     │
│                                                                     │
│     [Link: Ver Ordem]                                               │
└─────────────────────────────────────────────────────────────────────┘
         │
         ↓ (dependência)

┌─────────────────────────────────────────────────────────────────────┐
│ ▶️  2. Criar Encomenda a Fornecedor                                │
│     [EM PROGRESSO]                                                  │
│     👥 Gestor Comercial | 📅 19/11/2025                            │
│     ⚠️ Depende: Tarefa #1 ✅                                        │
│                                                                     │
│     [Botão: Atribuir] [Botão: Concluir]                            │
└─────────────────────────────────────────────────────────────────────┘
         │
         ↓

┌─────────────────────────────────────────────────────────────────────┐
│ 🔓  3. Receção em Armazém                                          │
│     [PENDENTE - DESBLOQUEADA]                                       │
│     👥 Gestor de Armazém | 📅 20/11/2025                           │
│     ⚠️ Depende: Tarefa #2 (em progresso)                           │
│                                                                     │
│     [Botão: Iniciar]                                                │
└─────────────────────────────────────────────────────────────────────┘
         │
         ↓

┌─────────────────────────────────────────────────────────────────────┐
│ 🔒  4. Recolha do Armazém                                          │
│     [PENDENTE - BLOQUEADA]                                          │
│     👥 Gestor de Armazém | 📅 21/11/2025                           │
│     ⚠️ Depende: Tarefa #3 (pendente) ❌                            │
│                                                                     │
│     (Bloqueada - aguarda tarefa anterior)                           │
└─────────────────────────────────────────────────────────────────────┘

... mais 5 tarefas bloqueadas ...
```

### Indicadores Visuais

| Ícone     | Significado                         | Pode Agir?        |
| --------- | ----------------------------------- | ----------------- |
| 🔒 Lock   | Tarefa bloqueada (depende de outra) | ❌ Não            |
| 🔓 Unlock | Tarefa desbloqueada (pronta)        | ✅ Sim - Iniciar  |
| ▶️ Play   | Tarefa em progresso                 | ✅ Sim - Concluir |
| ✅ Check  | Tarefa concluída                    | ❌ Já feita       |

### Ações Disponíveis (Gestores)

#### A. Atribuir Tarefa

**Quem pode:** Utilizadores com permissão `work-orders.update`

**Como:**

1. Clica no botão **"Atribuir"** numa tarefa pendente
2. Escolhe:
    - **Utilizador específico** (João, Maria, etc.)
    - **Grupo/Papel** (Gestor Comercial, Gestor de Armazém)
3. Clica **"Atribuir"**

**Resultado:**

-   Tarefa fica atribuída
-   Utilizador ou grupo vê a tarefa em "Minhas Tarefas"

#### B. Iniciar / Concluir

Funciona igual ao Método 1 (Dashboard Pessoal)

---

## 📱 Método 3: API/Script (Avançado)

### Usar Tinker (Linha de Comandos)

```bash
php artisan tinker
```

```php
// Ver todas as tarefas pendentes
$tasks = App\Models\WorkOrderTask::where('status', 'pendente')->get();

// Ver minhas tarefas
$user = auth()->user();
$myTasks = App\Models\WorkOrderTask::where('assigned_to', $user->id)
    ->orWhere('assigned_group', $user->getRoleNames()->first())
    ->get();

// Iniciar tarefa
$task = App\Models\WorkOrderTask::find(1);
$task->update([
    'status' => 'em_progresso',
    'assigned_to' => auth()->id()
]);

// Concluir tarefa
$task->complete('Observações da conclusão');
```

---

## ❓ Perguntas Frequentes

### 1. **Porque não consigo iniciar uma tarefa?**

**Resposta:** A tarefa está bloqueada porque depende de outra tarefa que ainda não foi concluída.

**Verificar:**

-   Vê a informação "Depende: Tarefa #X"
-   A tarefa anterior (#X) tem que estar **concluída** (✅)
-   Só então esta tarefa fica **desbloqueada** (🔓)

**Exemplo:**

```
Tarefa #4: Recolha do Armazém
Depende: Tarefa #3 (Receção em Armazém)

Se Tarefa #3 está PENDENTE → Tarefa #4 BLOQUEADA 🔒
Se Tarefa #3 está CONCLUÍDA → Tarefa #4 DESBLOQUEADA 🔓
```

---

### 2. **Não vejo nenhuma tarefa em "Minhas Tarefas"**

**Possíveis razões:**

-   Não há tarefas atribuídas a ti
-   Não há tarefas atribuídas ao teu grupo/papel
-   Todas as tarefas já foram concluídas

**Verificar:**

1. Vai a **"Todas as Ordens"**
2. Clica em **"Ver Detalhes"** numa ordem
3. Vê se há tarefas atribuídas ao teu papel (ex: Gestor Comercial)
4. Se não há, pede a um gestor para **atribuir** tarefas a ti

---

### 3. **O que acontece quando concluo uma tarefa?**

**Automaticamente:**

1. ✅ Tarefa marcada como concluída
2. 📝 Observações guardadas
3. ⏰ Data/hora de conclusão registada
4. 🔓 **Próxima tarefa desbloqueada** (se houver dependência)
5. 📊 **Progresso atualizado** (ex: 22% → 33%)
6. 📈 **Status da ordem pode mudar:**
    - Se ainda há tarefas pendentes → "pendente"
    - Se há tarefas em progresso → "em_progresso"
    - Se todas concluídas → "concluída"

---

### 4. **Posso saltar uma tarefa?**

**Não.** O sistema força **dependências sequenciais**.

**Exemplo:**

-   Não podes fazer "Embalamento" (tarefa #5) se "Recolha do Armazém" (tarefa #4) não estiver concluída
-   Isto garante que o processo é seguido corretamente

**Exceção:** Gestores com permissão `work-orders.update` podem:

-   Remover tarefas
-   Adicionar tarefas novas
-   Mudar ordem das tarefas (avançado)

---

### 5. **Como sei se uma tarefa está atrasada?**

**Indicador visual:**

-   🔴 **Texto vermelho** "(Atrasada)" aparece ao lado do prazo
-   Só para tarefas **pendentes** ou **em progresso**
-   Tarefas **concluídas** não mostram atraso

**Exemplo:**

```
Prazo: 15/11/2025 🔴 (Atrasada)
```

(hoje é 17/11/2025, tarefa ultrapassou prazo)

---

### 6. **Posso adicionar observações depois de concluir?**

**Não diretamente.** As observações são pedidas no momento de conclusão.

**Alternativa:**

-   Vê o **histórico de atividades** (Activity Log)
-   Lá ficam registadas todas as ações
-   Ou adiciona uma **nova tarefa** com as observações

---

### 7. **Quantas tarefas são criadas automaticamente?**

**Depende do tipo de entrega:**

**Envio (shipping):** 9 tarefas

1. Validar Stock
2. Criar Encomenda Fornecedor
3. Receção em Armazém
4. Recolha do Armazém
5. Embalamento
6. Criar Guia de Transporte
7. Agendar Recolha
8. Encomenda Enviada
9. Entregue ao Cliente

**Levantamento (pickup):** 7 tarefas

1. Validar Stock
2. Criar Encomenda Fornecedor
3. Receção em Armazém
4. Recolha do Armazém
5. Embalamento
6. Disponível para Levantamento
7. Entregue ao Cliente

---

### 8. **Posso criar Ordens de Trabalho manualmente?**

**Sim!** Para casos especiais (não relacionados com encomendas):

1. **Menu:** Ordens de Trabalho → Nova Ordem
2. Preenche:
    - Título
    - Descrição
    - Prioridade (baixa/normal/alta/urgente)
    - Encomenda associada (opcional)
3. **Adiciona tarefas manualmente:**
    - Tipo de tarefa
    - Título e descrição
    - Atribuir a utilizador ou grupo
    - Prazo
4. Clica **"Criar Ordem de Trabalho"**

---

## 🎓 Exemplo Prático Completo

### Cenário: Processar Encomenda do Cliente "João Ninguém"

**1. Encomenda Criada (Automático)**

```
Cliente João Ninguém fez encomenda #ORD-00026
→ Sistema cria WorkOrder automaticamente
→ Sistema gera 9 tarefas (tipo: envio)
```

**2. Gestor Comercial - Tarefa #1**

```
Login como: maria@inovcorp.pt (Gestor Comercial)
→ Menu: Ordens de Trabalho → Minhas Tarefas
→ Vê: "Validar Disponibilidade em Armazém"
→ Clica: [Iniciar]
→ Verifica stock no sistema
→ Clica: [Concluir]
→ Escreve: "50 unidades disponíveis em armazém A3"
→ Confirma
```

**Resultado:**

-   ✅ Tarefa #1 concluída
-   🔓 Tarefa #2 desbloqueada ("Criar Encomenda a Fornecedor")

**3. Gestor Comercial - Tarefa #2**

```
(Continua na mesma sessão)
→ Vê nova tarefa desbloqueada: "Criar Encomenda a Fornecedor"
→ Clica: [Iniciar]
→ Vai ao módulo Encomendas a Fornecedor
→ Cria encomenda EF-001
→ Volta a Ordens de Trabalho
→ Clica: [Concluir]
→ Escreve: "Encomenda EF-001 criada para Fornecedor XYZ"
→ Confirma
```

**Resultado:**

-   ✅ Tarefa #2 concluída
-   🔓 Tarefa #3 desbloqueada ("Receção em Armazém")
-   📊 Progresso: 22% (2/9 tarefas)

**4. Gestor de Armazém - Tarefa #3**

```
Login como: carlos@inovcorp.pt (Gestor de Armazém)
→ Menu: Ordens de Trabalho → Minhas Tarefas
→ Vê: "Receção em Armazém (se encomendado)"
→ Clica: [Iniciar]
→ Aguarda chegada de mercadoria...
(3 dias depois)
→ Mercadoria chega
→ Clica: [Concluir]
→ Escreve: "50 unidades recebidas. Lote #2025-123"
→ Confirma
```

**Resultado:**

-   ✅ Tarefa #3 concluída
-   🔓 Tarefa #4 desbloqueada ("Recolha do Armazém")
-   📊 Progresso: 33% (3/9 tarefas)

**... E assim sucessivamente até tarefa #9 (Entregue ao Cliente)**

**Final:**

```
📊 Progresso: 100% (9/9 tarefas)
✅ Status: Concluída
📅 Tempo total: 9 dias (conforme prazos)
📝 Histórico completo preservado em Activity Log
```

---

## 🔐 Permissões por Papel

| Papel                 | Ver Minhas Tarefas | Ver Todas Ordens | Criar Ordem | Atribuir Tarefas | Iniciar/Concluir     |
| --------------------- | ------------------ | ---------------- | ----------- | ---------------- | -------------------- |
| **Super Admin**       | ✅                 | ✅               | ✅          | ✅               | ✅                   |
| **Administrador**     | ✅                 | ✅               | ✅          | ✅               | ✅                   |
| **Gestor Comercial**  | ✅                 | ✅               | ✅          | ✅               | ✅                   |
| **Gestor Financeiro** | ✅                 | ✅               | ❌          | ✅               | ✅                   |
| **Gestor de Armazém** | ✅                 | ✅               | ❌          | ✅               | ✅                   |
| **Utilizador Normal** | ✅                 | ❌               | ❌          | ❌               | ✅ (só suas tarefas) |

---

## 📞 Suporte

**Dúvidas?** Contacta:

-   Email: suporte@inovcorp.pt
-   Telefone: +351 XXX XXX XXX
-   Documentação: `docs/work-orders-module.md`

---

**Última atualização:** 17 Nov 2025  
**Versão do Sistema:** v0.19.0
