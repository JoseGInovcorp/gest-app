# Configuração do MailHog para Testes de Email

**Data de Configuração:** 11 de Novembro de 2025  
**Status:** ✅ Testado e Funcionando

---

## 📋 Índice

1. [O que é o MailHog](#o-que-é-o-mailhog)
2. [Instalação](#instalação)
3. [Configuração do Laravel](#configuração-do-laravel)
4. [Como Usar](#como-usar)
5. [Comandos Úteis](#comandos-úteis)
6. [Resolução de Problemas](#resolução-de-problemas)
7. [Alternativas](#alternativas)

---

## 🎯 O que é o MailHog

O **MailHog** é uma ferramenta de teste de emails que captura todos os emails enviados pela aplicação **sem os enviar de verdade**. Perfeito para desenvolvimento e testes locais.

**Vantagens:**

-   ✅ Captura emails sem enviar para destinatários reais
-   ✅ Interface web para visualizar emails
-   ✅ Suporta anexos (PDFs, imagens, etc.)
-   ✅ Leve e fácil de usar
-   ✅ Não requer autenticação
-   ✅ Perfeito para desenvolvimento local

---

## 📥 Instalação

### Windows (64-bit)

#### Método 1: Download Manual

1. Acede a: https://github.com/mailhog/MailHog/releases
2. Baixa o ficheiro: `MailHog_windows_amd64.exe`
3. Cria a pasta `C:\MailHog\`
4. Guarda o ficheiro como `C:\MailHog\mailhog.exe`

#### Método 2: PowerShell (Automático)

```powershell
# Criar pasta e baixar MailHog
New-Item -ItemType Directory -Force -Path C:\MailHog
Invoke-WebRequest -Uri "https://github.com/mailhog/MailHog/releases/download/v1.0.1/MailHog_windows_amd64.exe" -OutFile "C:\MailHog\mailhog.exe"
```

#### Método 3: Via Chocolatey

```powershell
choco install mailhog -y
```

#### Método 4: Via Scoop

```powershell
scoop install mailhog
```

### Iniciar o MailHog

**Opção 1: Comando Único**

```powershell
Start-Process -FilePath "C:\MailHog\mailhog.exe"
```

**Opção 2: Duplo Clique**

-   Navega até `C:\MailHog\`
-   Faz duplo clique em `mailhog.exe`

**Opção 3: Criar Atalho**

-   Cria um atalho no Desktop
-   Aponta para `C:\MailHog\mailhog.exe`
-   Inicia sempre que necessário

---

## ⚙️ Configuração do Laravel

### Ficheiro `.env`

Atualiza as seguintes variáveis no ficheiro `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@gest-app.local"
MAIL_FROM_NAME="${APP_NAME}"
```

### Explicação das Configurações

| Variável            | Valor                    | Descrição                        |
| ------------------- | ------------------------ | -------------------------------- |
| `MAIL_MAILER`       | `smtp`                   | Usar protocolo SMTP              |
| `MAIL_HOST`         | `127.0.0.1`              | Servidor local (localhost)       |
| `MAIL_PORT`         | `1025`                   | Porta SMTP do MailHog            |
| `MAIL_USERNAME`     | `null`                   | Sem autenticação necessária      |
| `MAIL_PASSWORD`     | `null`                   | Sem password necessária          |
| `MAIL_ENCRYPTION`   | `null`                   | Sem encriptação (ambiente local) |
| `MAIL_FROM_ADDRESS` | `noreply@gest-app.local` | Email de origem                  |
| `MAIL_FROM_NAME`    | `${APP_NAME}`            | Nome da aplicação                |

### ⚠️ Importante

Após alterar o `.env`, **reinicia o servidor Laravel**:

```bash
# Se estiver a usar php artisan serve
# Para o servidor (Ctrl+C) e reinicia
php artisan serve

# Se estiver a usar Laravel Valet ou outro
# Limpa o cache de configuração
php artisan config:clear
```

---

## 🚀 Como Usar

### 1. Iniciar o MailHog

```powershell
Start-Process -FilePath "C:\MailHog\mailhog.exe"
```

### 2. Aceder à Interface Web

Abre o browser e acede a:

```
http://localhost:8025
```

### 3. Testar Envio de Email

#### Exemplo: Módulo de Faturas Fornecedor

**Passo 1 - Criar Fatura:**

1. Acede a: `http://localhost/supplier-invoices`
2. Clica em **"Nova Fatura"**
3. Preenche os dados:
    - **Data Fatura:** Hoje
    - **Data Vencimento:** Daqui a 30 dias
    - **Fornecedor:** Seleciona um fornecedor (certifica-te que tem email)
    - **Valor Total:** 1500.00 €
    - **Documento:** Anexa um PDF
    - **Estado:** Pendente
4. Clica em **"Guardar"**

**Passo 2 - Marcar como Paga:**

1. Edita a fatura criada
2. Muda o estado de **"Pendente"** para **"Paga"**
3. Modal aparece automaticamente: _"Pretende enviar o comprovativo ao Fornecedor?"_
4. Clica no botão **"Escolher ficheiro"**
5. Seleciona um PDF (comprovativo de pagamento)
6. Clica em **"Enviar"**

**Passo 3 - Verificar Email:**

1. Acede a `http://localhost:8025`
2. Deves ver o email na lista!
3. Clica no email para visualizar:
    - ✉️ **Assunto:** "Comprovativo de Pagamento - Fatura FF-2025-XXXX"
    - 👤 **Para:** email do fornecedor
    - 💼 **De:** noreply@gest-app.local
    - 📎 **Anexo:** PDF do comprovativo
    - 📄 **Corpo:** Detalhes da fatura formatados com logo da empresa

### 4. Funcionalidades da Interface MailHog

**Visualizar Emails:**

-   Lista completa de todos os emails capturados
-   Ordenação por data (mais recentes primeiro)

**Detalhes do Email:**

-   **Source:** Ver código-fonte completo (HTML + headers)
-   **Plain:** Versão texto simples
-   **MIME:** Estrutura MIME completa
-   **Download:** Baixar email (.eml)

**Ações:**

-   🗑️ **Delete:** Apagar email individual
-   🗑️ **Clear all:** Limpar todos os emails

---

## 🛠️ Comandos Úteis

### Verificar se MailHog está a Correr

```powershell
Get-Process -Name "mailhog" -ErrorAction SilentlyContinue
```

**Saída esperada:**

```
Handles  NPM(K)    PM(K)      WS(K)     CPU(s)     Id  SI ProcessName
-------  ------    -----      -----     ------     --  -- -----------
    xxx      xx    xxxxx      xxxxx       x.xx   xxxx   x mailhog
```

### Parar o MailHog

```powershell
Stop-Process -Name "mailhog" -Force
```

### Reiniciar o MailHog

```powershell
Stop-Process -Name "mailhog" -Force -ErrorAction SilentlyContinue
Start-Sleep -Seconds 2
Start-Process -FilePath "C:\MailHog\mailhog.exe"
```

### Verificar Portas em Uso

```powershell
# Verificar porta SMTP (1025)
netstat -ano | findstr :1025

# Verificar porta HTTP (8025)
netstat -ano | findstr :8025
```

---

## 🔍 Resolução de Problemas

### Problema 1: MailHog não inicia

**Sintoma:** Ao executar `mailhog.exe`, nada acontece.

**Solução:**

1. Verifica se já está a correr:
    ```powershell
    Get-Process -Name "mailhog"
    ```
2. Verifica se as portas estão livres:
    ```powershell
    netstat -ano | findstr :1025
    netstat -ano | findstr :8025
    ```
3. Para processos que estejam a usar as portas e tenta novamente

### Problema 2: Emails não aparecem no MailHog

**Sintoma:** Envias email mas não aparece na interface.

**Checklist:**

-   [ ] MailHog está a correr? (`Get-Process -Name "mailhog"`)
-   [ ] `.env` está correto? (`MAIL_MAILER=smtp`, `MAIL_PORT=1025`)
-   [ ] Cache do Laravel limpo? (`php artisan config:clear`)
-   [ ] Servidor Laravel reiniciado após alterar `.env`?

**Solução:**

```powershell
# 1. Para o MailHog
Stop-Process -Name "mailhog" -Force -ErrorAction SilentlyContinue

# 2. Limpa cache do Laravel
php artisan config:clear
php artisan cache:clear

# 3. Reinicia MailHog
Start-Process -FilePath "C:\MailHog\mailhog.exe"

# 4. Testa novamente
```

### Problema 3: "Connection refused" ao enviar email

**Sintoma:** Erro: `Connection refused [tcp://127.0.0.1:1025]`

**Causa:** MailHog não está a correr.

**Solução:**

```powershell
Start-Process -FilePath "C:\MailHog\mailhog.exe"
```

### Problema 4: Interface web não carrega

**Sintoma:** `http://localhost:8025` não abre.

**Solução:**

1. Verifica se MailHog está a correr
2. Verifica se a porta 8025 está livre:
    ```powershell
    netstat -ano | findstr :8025
    ```
3. Tenta aceder por IP direto: `http://127.0.0.1:8025`

### Problema 5: Anexos não aparecem

**Sintoma:** Email chega mas sem anexo PDF.

**Checklist:**

-   [ ] Ficheiro foi realmente enviado no POST?
-   [ ] Campo do formulário é `comprovativo` (não `comprovativo_pagamento`)?
-   [ ] `enctype="multipart/form-data"` está no formulário?
-   [ ] Storage está configurado? (`storage:link` executado?)

---

## 📊 Portas Utilizadas

| Porta  | Serviço | Descrição                            |
| ------ | ------- | ------------------------------------ |
| `1025` | SMTP    | Porta onde Laravel envia emails      |
| `8025` | HTTP    | Interface web para visualizar emails |

**URLs de Acesso:**

-   Interface Web: `http://localhost:8025`
-   API JSON: `http://localhost:8025/api/v2/messages`

---

## 🔄 Alternativas ao MailHog

### 1. Mailtrap.io (Online, Grátis)

**Vantagens:**

-   Interface moderna
-   Colaboração em equipa
-   Análise de spam score
-   Testes em múltiplos ambientes

**Configuração `.env`:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_username_mailtrap
MAIL_PASSWORD=seu_password_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gest-app.local"
MAIL_FROM_NAME="${APP_NAME}"
```

**Como obter credenciais:**

1. Cria conta em https://mailtrap.io
2. Cria um inbox
3. Copia username e password

### 2. Gmail (Real - Produção)

**⚠️ Apenas para produção, não desenvolvimento!**

**Configuração `.env`:**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=app_password_gerado
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="seu-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Gerar App Password:**

1. Acede a https://myaccount.google.com/security
2. Ativa verificação em 2 passos
3. Gera App Password para "Mail"
4. Usa essa password no `.env`

### 3. Log (Apenas Logs - Sem Interface)

**Configuração `.env`:**

```env
MAIL_MAILER=log
```

**Emails guardados em:**

```
storage/logs/laravel.log
```

**Vantagens:**

-   Muito simples
-   Zero configuração

**Desvantagens:**

-   Sem interface visual
-   Sem anexos renderizados
-   Difícil de ler

---

## 📝 Notas Importantes

### Para Desenvolvimento

✅ **Usar:** MailHog ou Mailtrap  
❌ **Não usar:** Gmail ou serviços reais

**Motivo:** Evita envio acidental de emails de teste para clientes reais.

### Para Produção

✅ **Usar:** Gmail, SendGrid, AWS SES, Mailgun, Postmark  
❌ **Não usar:** MailHog ou Mailtrap

**Motivo:** Serviços reais garantem entrega e têm melhor reputação.

### Segurança

-   ⚠️ Nunca commits o `.env` com credenciais reais
-   ⚠️ Usa variáveis de ambiente em produção
-   ⚠️ MailHog apenas aceita conexões locais (seguro)

---

## ✅ Checklist de Funcionamento

Usa esta checklist para confirmar que está tudo a funcionar:

-   [ ] MailHog instalado em `C:\MailHog\mailhog.exe`
-   [ ] MailHog a correr (verificar com `Get-Process -Name "mailhog"`)
-   [ ] Interface web acessível em `http://localhost:8025`
-   [ ] `.env` configurado com `MAIL_MAILER=smtp` e `MAIL_PORT=1025`
-   [ ] Cache do Laravel limpo (`php artisan config:clear`)
-   [ ] Servidor Laravel reiniciado
-   [ ] Teste de email enviado (exemplo: Fatura Fornecedor)
-   [ ] Email aparece no MailHog
-   [ ] Anexo PDF presente e descarregável
-   [ ] Formatação do email correta (logo, texto, dados)

---

## 🎓 Recursos Adicionais

**Documentação Oficial:**

-   MailHog: https://github.com/mailhog/MailHog
-   Laravel Mail: https://laravel.com/docs/11.x/mail

**Tutoriais:**

-   Como criar Mailables: https://laravel.com/docs/11.x/mail#generating-mailables
-   Testes de email: https://laravel.com/docs/11.x/mail#testing-mailables

---

## 📧 Exemplo de Uso no Código

### Enviar Email Simples

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentProofMail;

// Enviar email
Mail::to('fornecedor@exemplo.com')->send(
    new PaymentProofMail($invoice, $company, $proofPath)
);
```

### Testar com Mail::fake()

```php
use Illuminate\Support\Facades\Mail;

// Em testes
Mail::fake();

// ... código que envia email ...

// Verificar que foi enviado
Mail::assertSent(PaymentProofMail::class, function ($mail) {
    return $mail->hasTo('fornecedor@exemplo.com');
});
```

---

## 🔧 Manutenção

### Limpar Emails Antigos do MailHog

O MailHog guarda emails em memória. Ao reiniciar, todos são apagados.

**Limpar manualmente via interface:**

-   Acede a `http://localhost:8025`
-   Clica em **"Clear all"**

**Limpar reiniciando:**

```powershell
Stop-Process -Name "mailhog" -Force
Start-Process -FilePath "C:\MailHog\mailhog.exe"
```

### Atualizar MailHog

1. Para o processo atual
2. Baixa nova versão de https://github.com/mailhog/MailHog/releases
3. Substitui o ficheiro em `C:\MailHog\mailhog.exe`
4. Reinicia

---

## ✅ Status da Configuração Atual

**Data do Último Teste:** 11 de Novembro de 2025  
**Resultado:** ✅ Funcionando Perfeitamente  
**Módulo Testado:** Faturas Fornecedor  
**Email Enviado:** Comprovativo de Pagamento com anexo PDF  
**Recepção:** Confirmada no MailHog (http://localhost:8025)

---

**Configuração validada e pronta para uso! 🚀**
