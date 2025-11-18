# 🏢 Gest-App — Sistema de Gestão Empresarial

> Projeto Final de Estágio na Inovcorp

## 📊 Sobre o Projeto

Este é o meu projeto final de estágio, desenvolvido na **Inovcorp** entre 6 e 18 de Novembro de 2025.

O objetivo é criar um sistema de gestão empresarial para PMEs, com funcionalidades de gestão comercial, financeira e operacional.

**Versão Atual:** v0.26.0  
**Progresso:** 100% (20 de 20 módulos concluídos + Dashboard + Gestão Stock + **Integração Financeira** + **Segurança Reforçada** + **Cifra RGPD**)  
**Prazo de Entrega:** 18 Nov 2025

## ✅ O que já está pronto

-   ✅ MySQL configurado e a funcionar
-   ✅ **Sistema de permissões completo e efetivo**
-   ✅ **Controlo de acesso baseado em roles granulares**
-   ✅ **UI adaptativa às permissões do utilizador**
-   ✅ **Cifra AES-256-CBC de dados sensíveis (RGPD)**
-   ✅ **13 campos pessoais cifrados automaticamente**
-   ✅ Histórico de atividades (logs)
-   ✅ Logo da empresa integrado
-   ✅ **Dashboard adaptativo baseado em permissões**
-   ✅ **Gestão automática de stock nas encomendas**
-   ✅ **Integração financeira automática** (Encomendas → Conta Corrente + Banco)
-   ✅ **Movimentos manuais sincronizados com banco**
-   ✅ **Arquivo digital com autenticação segura**
-   ✅ **Interface 100% uniformizada e consistente**
-   ✅ **Componente ConfirmDialog reutilizável**
-   ✅ **Botões de ação padronizados (cinza/azul/vermelho)**
-   ✅ **Headers de página com estrutura consistente**
-   ✅ **Export PDF de extratos bancários**
-   ✅ Módulo financeiro (contas bancárias, conta corrente, faturas)
-   ✅ Sistema de email configurado
-   ✅ Testes automatizados
-   ✅ Calendário com FullCalendar
-   ✅ **Dark mode completo em toda aplicação**

## 🛠️ Tecnologias Usadas

-   **Backend:** Laravel 12
-   **Frontend:** Vue.js 3 + Inertia.js
-   **UI:** Tailwind CSS + Shadcn/ui
-   **Base de Dados:** MySQL
-   **Permissões:** Spatie Laravel Permission
-   **Email:** Laravel Mail + MailHog (desenvolvimento)
-   **Testes:** PHPUnit

## 📦 Módulos Implementados

### Módulo 1: Entidades (Clientes/Fornecedores)

-   CRUD completo com validação de NIF
-   Integração com VIES para buscar dados de empresas da UE
-   Tabela com filtros e pesquisa
-   Numeração automática

### Módulo 2: Contactos

-   Associados a clientes/fornecedores
-   Campos: nome, função, telefones, email
-   Checkbox para consentimento RGPD
-   Interface moderna

### Módulo 3: Artigos (Produtos/Serviços)

-   CRUD completo com referências automáticas (ART001, ART002...)
-   Upload de imagens com preview
-   Dropdown de taxas IVA carregado da BD
-   Estados Ativo/Inativo
-   **Controlo de stock (quantidade disponível)**
-   **Tipo: Produto ou Serviço**
-   **Gama: Premium, Standard, Básico**

### Gestão de Stock

-   ✅ **Validação automática ao criar/editar encomendas**
-   ✅ **Indicadores visuais em tempo real** (verde/laranja/vermelho)
-   ✅ **Alertas de stock insuficiente**
-   ✅ **Encomenda pode prosseguir sem stock** (com aviso)
-   ✅ **Sugestão de criar encomenda ao fornecedor**
-   ✅ **Atualização automática ao fechar encomenda**
-   ✅ **Reposição automática ao reabrir encomenda**
-   ✅ **Serviços não afetam stock**
-   ✅ **Stock nunca fica negativo**

### Módulo 4: Países (Configurações)

-   CRUD para gestão de países
-   Códigos ISO, prefixo telefone, moeda
-   14 países já pré-carregados
-   Usado nos dropdowns de Clientes/Fornecedores

### Módulo 5: Funções de Contacto (Configurações)

-   CRUD para funções (Diretor Geral, Comercial, etc.)
-   10 funções pré-definidas
-   Usado no dropdown de Contactos

### Módulo 6: Taxas de IVA (Configurações)

-   CRUD para taxas de IVA
-   4 taxas pré-carregadas: 0%, 6%, 13%, 23%
-   Sistema de taxa padrão
-   Integrado nos formulários de Artigos

### Módulo 7: Gestão de Acessos

**Utilizadores:**

-   CRUD completo (nome, email, telemóvel, grupo, estado)

**Permissões:**

-   Sistema com 68 permissões (17 módulos × 4 ações)
-   6 grupos criados: Super Admin, Administrador, Gestor Comercial, Gestor Financeiro, Editor, Visualizador
-   Botões só aparecem se o utilizador tiver permissão (zero erros 403)
-   Utilizador "Visualizador" só vê listas, sem botões de criar/editar/eliminar

#### Permissões por Grupo

| Grupo             | Permissões | Acesso                                               |
| ----------------- | ---------- | ---------------------------------------------------- |
| Super Admin       | 68 (100%)  | Todos os módulos                                     |
| Administrador     | 64 (94%)   | Todos exceto permissões                              |
| Gestor Comercial  | 32 (47%)   | Entidades, contactos, artigos, propostas, encomendas |
| Gestor Financeiro | 28 (41%)   | Contas bancárias, conta corrente, faturas            |
| Editor            | 48 (71%)   | Todos exceto gestão de acessos e configurações       |
| Visualizador      | 17 (25%)   | Apenas visualizar (sem criar/editar/eliminar)        |

### Módulo 8: Histórico de Atividades

-   **100% dos controllers** com activity logging implementado
-   Package: Spatie Laravel Activitylog v4.10
-   Registo automático de todas as ações: create, update, delete
-   Captura de contexto completo: IP, user agent, deleted entity details
-   Tabela com 7 colunas: Data, Hora, Utilizador, Menu, Ação, Dispositivo, IP
-   18 módulos mapeados com labels em português
-   Detecção automática de dispositivo (Desktop/Mobile/Tablet)
-   Badges coloridos por tipo de ação (created=verde, updated=azul, deleted=vermelho)

**Cobertura de Logging:**

-   ✅ Config (5): Contact, Article, Country, ContactFunction, VatRate
-   ✅ Business (6): Proposal, CustomerOrder, SupplierOrder, BankAccount, ClientAccount, SupplierInvoice
-   ✅ Calendar (3): CalendarEvent, CalendarEventType, CalendarEventAction
-   ✅ Settings (1): Company
-   ✅ Auth (2): Login, Logout
-   ✅ Access (2): User, Role

**Propriedades Capturadas:**

-   IP address em todos os logs
-   User agent (browser info) em todos os logs
-   Deleted entity details (antes de eliminar)
-   Propriedades especiais: items_count, lines_count, logo_updated

### Módulo 9: Contas Bancárias

-   CRUD completo com validação de IBAN
-   Campos: banco, número conta, IBAN, SWIFT/BIC, moeda
-   Estados Ativa/Inativa
-   Checkbox para conta padrão
-   **Extrato de movimentos com saldo após cada transação**
-   **Export PDF profissional do extrato**
-   **Atualização automática de saldo** (observers)
-   **Integração com encomendas fechadas**

### Módulo 10: Conta Corrente Bancária

-   Registo de movimentos bancários (crédito/débito)
-   Tabela com saldo automaticamente calculado
-   Filtros por conta, tipo, período
-   Modal de criação rápida
-   Associação a entidades e documentos
-   Reconciliação bancária
-   **Cálculo automático de `saldo_apos`** em cada transação

### Módulo 11: Faturas de Fornecedores

-   CRUD completo (número, data, vencimento, fornecedor)
-   Upload de comprovativo de pagamento (PDF, imagens)
-   Dropdown de fornecedores carregado dinamicamente
-   Estados: Pendente, Pago, Vencido, Cancelado
-   Envio automático de email com comprovativo
-   Validação de datas e valores

### Módulo 12: Conta Corrente de Clientes

-   Tabela de saldo por cliente
-   Registo de documentos (faturas, recibos, notas)
-   Saldo automaticamente calculado
-   Filtros por cliente, tipo documento, período
-   Mostra débitos, créditos e saldo atual
-   **Criação automática de movimento ao fechar encomenda**
-   **Débito = Cliente pagou (reduz dívida)**
-   **Crédito = Cliente deve (aumenta dívida)**
-   Integração com faturas de clientes (quando criado)

### Módulo 13: Calendário

-   Interface com FullCalendar v6
-   Criação, edição, eliminação de eventos
-   Tipos de eventos (Reunião, Visita Cliente, Tarefa)
-   Estados com cores (Planeado, Concluído, Cancelado)
-   Ações de follow-up (Email, Chamada, Proposta)
-   Vista mensal interativa
-   Drag & drop para reagendar

### Módulo 14: Tipos de Eventos (Configurações)

-   CRUD para tipos de eventos
-   4 tipos pré-definidos: Reunião, Visita Cliente, Tarefa, Formação
-   Cor personalizada para cada tipo
-   Estados Ativo/Inativo
-   Usado no dropdown de criação de eventos

### Módulo 15: Ações de Eventos (Configurações)

-   CRUD para ações de follow-up
-   4 ações pré-definidas: Enviar Email, Fazer Chamada, Enviar Proposta, Agendar Visita
-   Estados Ativo/Inativo
-   Usado no campo de próxima ação dos eventos

### Módulo 16: Propostas Comerciais (v0.20.0)

Criação e gestão de propostas a clientes com workflow completo e business rules.

**Campos:**

-   Numeração automática: PROP-YEAR-#### (ex: PROP-2025-0001)
-   Data proposta (condicional), validade (auto-calculada +30 dias)
-   Cliente (entidade)
-   Estado: Rascunho, Fechado
-   Observações

**Business Rules (v0.20.0):**

-   **Rascunho:** data_proposta e validade opcionais
-   **Fechado:** data_proposta obrigatória, validade auto-calculada (+30 dias)
-   Validação condicional baseada em estado
-   Auto-fill de preços com IVA ao selecionar artigo

### Módulo 17: Arquivo Digital (v0.17.0)

Sistema completo de gestão de documentos digitais com versioning e metadata.

**Funcionalidades:**

-   Upload de documentos (max 10MB): PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
-   Drag & drop interface para facilitar uploads
-   Preview de PDF (iframe) e imagens diretamente no browser
-   Sistema de versioning (histórico de versões)
-   9 categorias de documentos com cores:
    -   Contrato (azul), Fatura (vermelho), Proposta (verde)
    -   Identificação (roxo), Certificado (amarelo), Relatório (índigo)
    -   Comprovativo (rosa), Correspondência (ciano), Outros (cinza)
-   Metadata completa: descrição, tags (JSON), data de expiração
-   Associação polimórfica com módulos: Clientes, Fornecedores, Propostas, Encomendas
-   Filtros avançados: pesquisa, categoria, módulo, período
-   Grid view responsivo (1-4 colunas)
-   Soft deletes (documentos podem ser recuperados)
-   Download de documentos com nome original
-   Permissões granulares (create, read, edit, delete)
-   Dashboard com estatísticas (total documentos, tamanho, próximos a expirar)

**Estados:**

-   Ativo, Arquivado, Eliminado (soft delete)

**Interface:**

-   Index: Grid com cards de documentos, ícones por tipo de ficheiro
-   Show: Preview + sidebar com metadata, versões anteriores
-   Upload Modal: Custom modal com drag & drop, preview antes de submeter
-   Observações

**Linhas de Proposta:**

-   Artigo, quantidade, preço de custo, fornecedor
-   Cálculo automático do total de cada linha
-   Valor total da proposta calculado automaticamente
-   Adição/remoção dinâmica de linhas

**Funcionalidades:**

-   ✅ Botão "Converter para Encomenda" (aparece quando estado = fechado)
-   ✅ Gera Encomenda Cliente em rascunho
-   ✅ Download PDF profissional com:
    -   Logo da empresa no cabeçalho
    -   Informação cliente em 2 colunas
    -   Tabela de artigos
    -   Observações incluídas na tabela de detalhes
    -   Layout otimizado para A4

**Validações:**

-   Artigo obrigatório
-   Quantidade > 0
-   Preço de custo ≥ 0
-   Total calculado automaticamente

### Módulo 18: Encomendas Cliente (v0.15.0 - v0.24.0)

Gestão de encomendas de clientes com conversão para encomendas de fornecedores e **integração financeira automática**.

**Campos:**

-   Numeração automática: EC-YEAR-#### (ex: EC-2025-0001)
-   Data, validade, cliente, proposta origem (opcional)
-   Estado: Rascunho, Em Curso, Concluído, Cancelado, Faturado, **Closed**
-   Notas

**Funcionalidades:**

-   ✅ CRUD completo herdado (já existia como "Encomendas")
-   ✅ Conversão multi-fornecedor para Encomendas Fornecedor
-   ✅ **Integração financeira automática (v0.24.0)**:
    -   Quando status muda para "closed"
    -   Cria movimento **DÉBITO** na Conta Corrente Cliente
    -   Cria movimento **CRÉDITO** na Conta Bancária
    -   Ambos relacionados com referência da encomenda
    -   Observer CustomerOrderObserver
-   ✅ **Gestão automática de stock (v0.23.0)**:
    -   Validação ao criar/editar
    -   Decremento ao fechar
    -   Reposição ao reabrir
-   ✅ Download PDF profissional:
    -   Título "ENCOMENDA CLIENTE"
    -   Layout matching Propostas
    -   Informação cliente e artigos
    -   Botão FileText roxo (#9333ea)

### Módulo 19: Encomendas Fornecedor (v0.15.0)

Gestão de encomendas a fornecedores, criadas automaticamente ou manualmente.

**Campos:**

-   Numeração automática: EF-YEAR-#### (ex: EF-2025-0001)
-   Data, data entrega, fornecedor
-   Encomenda cliente origem (opcional)
-   Estado: Rascunho, Enviado, Confirmado, Recebido, Cancelado
-   Notas

**Funcionalidades:**

-   ✅ CRUD completo
-   ✅ Download PDF profissional:
    -   Título "ENCOMENDA FORNECEDOR"
    -   Informação fornecedor (não cliente)
    -   Data de entrega em destaque
    -   Botão FileText roxo
    -   Layout A4 otimizado

### Módulo 20: Ordens de Trabalho (v0.19.0 - v0.20.0)

Sistema completo de gestão de tarefas e workflow automation com templates configuráveis.

**Funcionalidades Principais:**

-   ✅ Workflow automático ao criar Customer Order
-   ✅ 12 templates de tarefas configuráveis (DB)
-   ✅ Sistema de dependências sequenciais
-   ✅ Atribuição automática a grupos
-   ✅ Cálculo automático de prazos
-   ✅ Dashboard "Minhas Tarefas" com filtros (cliente, estado, atrasadas)
-   ✅ Timeline visual de progresso
-   ✅ Status automático baseado em conclusão de tarefas

**Task Templates (v0.20.0):**

CRUD completo em Configurações para gerir templates:

1. VALIDATE_STOCK - Validar Stock
2. ORDER_SUPPLIER - Encomendar a Fornecedor
3. WAREHOUSE_COLLECT - Recolher no Armazém
4. RECEIVE_GOODS - Receção de Mercadoria
5. PACK_ORDER - Embalar Encomenda
6. TRANSPORT_GUIDE - Gerar Guia de Transporte
7. SCHEDULE_TRANSPORT - Agendar Transporte
8. SEND_ORDER - Enviar Encomenda
9. PICKUP_ORDER - Levantamento pelo Cliente
10. DELIVER_ORDER - Entregar ao Cliente
11. CONFIRM_ORDER - Confirmar Encomenda
12. CREATE_CUSTOMER_INVOICE - Criar Fatura de Cliente

**Interface:**

-   MyTasks.vue: Dashboard pessoal com filtros (cliente, estado, atrasadas)
-   Index.vue: Todas as ordens com filtros avançados
-   Show.vue: Timeline com progresso visual
-   TaskTemplates: CRUD em Configurações (shadcn-vue)

**Workflow:**

-   Automático ao criar encomenda de cliente
-   Duas rotas: Envio (9 tarefas) vs Levantamento (7 tarefas)
-   100% configurável via DB sem alterar código

**Permissions:**

-   work-orders.create/read/update/delete
-   task-templates.create/read/update/delete

## 📋 Sistema Completo

**Total de Módulos:** 20 ✅  
**Status:** 100% Concluído

### Módulos Core (20)

1. ✅ Entidades (Clientes/Fornecedores)
2. ✅ Contactos
3. ✅ Artigos
4. ✅ Países
5. ✅ Funções de Contacto
6. ✅ Taxas de IVA
7. ✅ Gestão de Acessos (Utilizadores + Permissões)
8. ✅ Histórico de Atividades
9. ✅ Configurações da Empresa
10. ✅ Contas Bancárias
11. ✅ Faturas de Fornecedores
12. ✅ Conta Corrente Bancária
13. ✅ Conta Corrente de Clientes
14. ✅ Calendário de Eventos
15. ✅ Tipos de Eventos
16. ✅ Ações de Eventos
17. ✅ Propostas Comerciais
18. ✅ Arquivo Digital
19. ✅ Encomendas Cliente
20. ✅ Encomendas Fornecedor
21. ✅ Ordens de Trabalho + Task Templates

## 🔧 Como Executar

### 1. Configurar a Base de Dados

```bash
# Copiar .env.example para .env
cp .env.example .env

# Editar .env com credenciais do MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gest_app
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Instalar Dependências

```bash
composer install
npm install
```

### 3. Preparar a Aplicação

```bash
# Gerar chave
php artisan key:generate

# Executar migrations e seeders
php artisan migrate:fresh --seed

# Compilar assets
npm run build
```

### 4. Iniciar Servidores

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (desenvolvimento)
npm run dev
```

**URL:** http://localhost:8000

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Testes com cobertura
php artisan test --coverage
```

## 📧 Email (Desenvolvimento)

Para testar envio de emails localmente, uso o MailHog:

```bash
# Instalar (Windows com Chocolatey)
choco install mailhog

# Executar
mailhog

# Ver emails em: http://localhost:8025
```

Configuração no `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
```

## 📁 Estrutura do Projeto

```
app/
├── Models/              # 20+ modelos (Eloquent ORM)
├── Http/Controllers/    # Controllers para cada módulo
├── Mail/                # Classes de email
└── Policies/            # Autorizações

resources/
├── js/Pages/            # Componentes Vue 3
└── views/               # Templates Blade (PDFs)

database/
├── migrations/          # 40+ migrations
└── seeders/             # Dados iniciais

docs/                    # Documentação do projeto
```

## 📚 Documentação Técnica

Cada módulo tem documentação própria em `docs/`:

-   `access-management.md` - Sistema de permissões
-   `bank-accounts-module.md` - Módulo financeiro
-   `client-accounts-module.md` - Conta corrente clientes
-   `customer-orders-module.md` - Encomendas e PDFs
-   `database-config.md` - Configuração MySQL
-   `stock-management.md` - Gestão automática de stock
-   `work-orders-module.md` - Workflow e tarefas
-   `changelog.md` - Histórico completo de versões
-   `relatorio-progresso.md` - Relatório detalhado do estágio

## 🎯 Funcionalidades Destacadas

### Automação Financeira (v0.24.0)

Sistema completamente automatizado de integração entre módulos:

1. **Encomenda Cliente fechada** → Automático:

    - ✅ Cria movimento na Conta Corrente Cliente (débito)
    - ✅ Cria movimento na Conta Bancária (crédito)
    - ✅ Atualiza saldos automaticamente
    - ✅ Relaciona com referência da encomenda

2. **Laravel Observers** para automação:

    - `CustomerOrderObserver` - Detecta status closed
    - `BankTransactionObserver` - Calcula saldos
    - `ClientAccountObserver` - Atualiza conta corrente

3. **Rastreabilidade total**:
    - Cada movimento tem referência à encomenda
    - Nome do cliente nas observações
    - Saldo após cada movimento
    - Export PDF do extrato completo

### Gestão de Stock (v0.23.0)

-   Validação em tempo real ao criar/editar encomendas
-   Indicadores visuais (verde/laranja/vermelho)
-   Alertas de stock insuficiente
-   Decremento automático ao fechar encomenda
-   Reposição automática ao reabrir
-   Serviços não afetam stock

### Interface Consistente

-   Headers padronizados em todas as páginas Show
-   Ícones em containers coloridos
-   Botões com componente reutilizável
-   Layout: Ícone+Título | Botões de ação
-   Dark mode em 100% da aplicação

---

**Desenvolvido por:** [Nome]  
**Orientador:** [Nome do Orientador]  
**Empresa:** Inovcorp  
**GitHub:** [github.com/JoseGInovcorp/gest-app](https://github.com/JoseGInovcorp/gest-app)

-   `mailhog-setup.md` - Setup de email
-   `changelog.md` - Histórico de versões
-   `relatorio-progresso.md` - Progresso diário

## ⚖️ Licença

Este projeto foi desenvolvido durante o estágio na **Inovcorp** (Novembro 2025).

Código open-source sob licença MIT.
