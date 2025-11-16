# 🚀 DEPLOYMENT PARA PRODUÇÃO - DIA 18 NOVEMBRO 2025

**⚠️ CHECKLIST CRÍTICO - SEGUIR POR ORDEM**

---

## 📋 ANTES DE IR PARA PRODUÇÃO (Fazer AGORA - Dia 16/17)

### 1. Backup Completo

```bash
# Backup da base de dados
mysqldump -u root -p gest_app > C:\Backups\gest-app\database_PRE_ENCRYPTION_$(Get-Date -Format 'yyyyMMdd_HHmm').sql

# Backup do .env
Copy-Item .env -Destination C:\Backups\gest-app\.env.PRE_PRODUCTION_$(Get-Date -Format 'yyyyMMdd')

# Backup do projeto completo
Compress-Archive -Path C:\Inovcorp\gest-app -DestinationPath C:\Backups\gest-app\projeto_completo_$(Get-Date -Format 'yyyyMMdd').zip
```

### 2. Guardar APP_KEY (CRÍTICO!)

```bash
# Copiar APP_KEY para Bitwarden/KeePass
Get-Content .env | Select-String "APP_KEY"

# Resultado: APP_KEY=base64:Tsi6B8tDsESrlBrkQBEJBrnun0n8oENyI/2JimzI+Pw=
```

✅ **Guardar em:**

-   [ ] Bitwarden/KeePass (password manager)
-   [ ] Documento Word protegido em pen USB
-   [ ] Cloud (OneDrive/Google Drive) - ficheiro encriptado

---

## 🔐 NO DIA DA PRODUÇÃO (Dia 18)

### PASSO 1: Preparar Ambiente de Produção

```bash
# 1.1 Atualizar .env para produção
```

Editar `.env`:

```env
APP_NAME="Gest-App"
APP_ENV=production
APP_KEY=base64:Tsi6B8tDsESrlBrkQBEJBrnun0n8oENyI/2JimzI+Pw=
APP_DEBUG=false
APP_URL=https://seudominio.com  # Mudar para domínio real

# Base de dados (verificar credenciais de produção)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gest_app_production
DB_USERNAME=root
DB_PASSWORD=password_segura  # Mudar para password real
```

```bash
# 1.2 Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

### PASSO 2: Ativar Encriptação

**2.1 Ativar encrypted casts nos models:**

Editar `app/Models/Entity.php`:

```php
protected $casts = [
    'vies_valid' => 'boolean',
    'vies_last_check' => 'datetime',
    'vies_data' => 'array',
    'different_billing_address' => 'boolean',
    'credit_limit' => 'decimal:2',
    'discount_percentage' => 'decimal:2',
    'tax_exempt' => 'boolean',
    'active' => 'boolean',
    'custom_fields' => 'array',
    // REMOVER comentários e ATIVAR:
    'tax_number' => 'encrypted',
    'phone' => 'encrypted',
    'mobile' => 'encrypted',
    'email' => 'encrypted',
    'iban' => 'encrypted',
];
```

Editar `app/Models/Contact.php`:

```php
protected $casts = [
    'rgpd_consent' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    // REMOVER comentários e ATIVAR:
    'phone' => 'encrypted',
    'mobile' => 'encrypted',
    'email' => 'encrypted',
];
```

Editar `app/Models/BankAccount.php`:

```php
protected $casts = [
    'saldo_inicial' => 'decimal:2',
    'saldo_atual' => 'decimal:2',
    // REMOVER comentários e ATIVAR:
    'iban' => 'encrypted',
    'swift_bic' => 'encrypted',
];
```

**2.2 Encriptar dados:**

```bash
# Executar comando de encriptação
php artisan security:encrypt-data --force

# Verificar que não há erros
php artisan about
```

**2.3 Testar encriptação:**

```bash
# Aceder à aplicação
# Ir para Clientes, Fornecedores, Contactos
# Verificar se NIF, telefones, emails aparecem NORMALMENTE
# Se aparecer "The payload is invalid" → PROBLEMA!
```

---

### PASSO 3: Configurar HTTPS (Se aplicável)

```bash
# 3.1 Instalar certificado SSL (Let's Encrypt)
# (Depende do servidor - Nginx/Apache)

# 3.2 Verificar middleware ForceHttps
# Já está configurado em:
# - app/Http/Middleware/ForceHttps.php
# - app/Providers/AppServiceProvider.php
# - bootstrap/app.php

# 3.3 Testar redirect
# Aceder via HTTP → deve redirecionar para HTTPS
```

---

### PASSO 4: Build e Cache

```bash
# 4.1 Build de assets para produção
npm run build

# 4.2 Cache de configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4.3 Otimizar autoloader
composer install --optimize-autoloader --no-dev
```

---

### PASSO 5: Permissões e Segurança

```bash
# 5.1 Permissões corretas
# Windows (Herd/XAMPP):
icacls storage /grant Users:(OI)(CI)F /T
icacls bootstrap\cache /grant Users:(OI)(CI)F /T

# Linux:
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 5.2 Verificar .env não é público
# .env deve estar FORA de public_html
# Deve estar no .gitignore
```

---

### PASSO 6: Testes Finais

```bash
# 6.1 Testar login
✓ Autenticação funciona?
✓ 2FA configurado e a funcionar?

# 6.2 Testar módulos principais
✓ Clientes (dados encriptados aparecem?)
✓ Fornecedores (NIF visível?)
✓ Contactos (telefones visíveis?)
✓ Propostas (valores corretos?)
✓ Encomendas (funcionam?)
✓ Faturas (PDFs geram?)
✓ Arquivo Digital (downloads funcionam?)

# 6.3 Testar segurança
✓ HTTP redireciona para HTTPS?
✓ Acesso direto a ficheiros bloqueado?
✓ Login obrigatório em todas as páginas?
✓ Permissões funcionam corretamente?
```

---

## ✅ CHECKLIST FINAL

Antes de entregar, confirmar:

### Segurança

-   [ ] APP_KEY guardado em 3 locais seguros
-   [ ] Dados sensíveis encriptados (NIF, IBAN, telefones, emails)
-   [ ] HTTPS ativo (se em servidor real)
-   [ ] Documentos privados fora de public_html
-   [ ] .env com APP_DEBUG=false
-   [ ] .env com APP_ENV=production

### Funcionalidades

-   [ ] Todos os 19 módulos funcionam
-   [ ] Permissões corretas por role
-   [ ] 2FA configurado
-   [ ] Logs de atividade ativos
-   [ ] Arquivo Digital a funcionar
-   [ ] Relatórios e exports funcionam

### Documentação

-   [ ] Changelog atualizado (v0.18.0)
-   [ ] Compliance-check mostra 100%
-   [ ] README do projeto completo
-   [ ] Documentação de segurança (privada)

---

## 🚨 TROUBLESHOOTING

### Erro: "The payload is invalid"

**Causa:** APP_KEY mudou ou dados não foram encriptados corretamente

**Solução:**

```bash
# 1. Verificar APP_KEY não mudou
Get-Content .env | Select-String "APP_KEY"
# Deve ser: base64:Tsi6B8tDsESrlBrkQBEJBrnun0n8oENyI/2JimzI+Pw=

# 2. Se mudou, RESTAURAR de backup
Copy-Item C:\Backups\gest-app\.env.PRE_PRODUCTION_* -Destination .env

# 3. Limpar cache
php artisan config:clear
php artisan cache:clear

# 4. Re-executar encriptação
php artisan security:encrypt-data --force
```

### Erro: Páginas em branco

**Causa:** Cache de configuração desatualizado

**Solução:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Erro: Assets não carregam

**Causa:** Build de produção não executado

**Solução:**

```bash
npm run build
php artisan view:clear
```

---

## 📞 CONTACTOS DE EMERGÊNCIA

**Se algo correr mal:**

1. **RESTAURAR BACKUP**

    ```bash
    # Base de dados
    mysql -u root -p gest_app_production < C:\Backups\gest-app\database_PRE_ENCRYPTION_*.sql

    # .env
    Copy-Item C:\Backups\gest-app\.env.PRE_PRODUCTION_* -Destination .env
    ```

2. **DESATIVAR ENCRIPTAÇÃO (temporário)**
    - Comentar encrypted casts nos 3 models
    - Executar: `php artisan security:decrypt-data --force`
    - Limpar cache: `php artisan config:clear`

---

## 📊 TIMELINE RECOMENDADO

**Dia 17 (Véspera):**

-   ✅ Fazer todos os backups
-   ✅ Guardar APP_KEY em locais seguros
-   ✅ Testar procedimento em ambiente de teste
-   ✅ Preparar .env de produção

**Dia 18 (Produção):**

-   09h00 - Atualizar .env para produção
-   09h30 - Ativar encrypted casts nos models
-   10h00 - Executar php artisan security:encrypt-data
-   10h30 - Testes de funcionalidade
-   11h00 - Build de produção (npm run build)
-   11h30 - Cache de configurações
-   12h00 - Testes finais
-   14h00 - **ENTREGA**

---

**Última atualização:** 16 Novembro 2025  
**Versão:** v0.18.0  
**Status:** ✅ PRONTO PARA PRODUÇÃO
