# Cifra de Dados Sensíveis — Gest-App

## Visão Geral

Para garantir a conformidade com o RGPD e proteger dados pessoais, todos os campos sensíveis na base de dados são automaticamente cifrados usando **AES-256-CBC**, o algoritmo de cifra simétrica padrão do Laravel.

## Como Funciona

O Laravel fornece o **Encrypted Casting** que cifra dados automaticamente ao guardar na base de dados e os decifra ao ler. Isto é totalmente transparente para o código da aplicação.

### Processo Automático

```php
// Ao GUARDAR (Create/Update)
$entity->email = 'cliente@example.com';
$entity->save();
// ✅ Laravel cifra automaticamente antes de inserir na BD
// Na BD: "eyJpdiI6IjRxM3o2..." (texto cifrado)

// Ao LER
$email = $entity->email;
// ✅ Laravel decifra automaticamente
// Retorna: "cliente@example.com" (texto limpo)
```

### Chave de Cifra

A cifra usa a chave `APP_KEY` definida no ficheiro `.env`:

```env
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

⚠️ **CRÍTICO**: Esta chave NÃO DEVE ser alterada após dados serem cifrados, ou todos os dados cifrados ficarão ilegíveis permanentemente.

## Campos Cifrados

### 1. Entidades (Clientes/Fornecedores)

**Model:** `App\Models\Entity`

| Campo        | Tipo   | Motivo                            |
| ------------ | ------ | --------------------------------- |
| `tax_number` | string | NIF/VAT - dado fiscal sensível    |
| `phone`      | string | Número de telefone - dado pessoal |
| `mobile`     | string | Telemóvel - dado pessoal          |
| `email`      | string | Email - dado pessoal              |
| `iban`       | string | IBAN - dado bancário sensível     |

**Implementação:**

```php
use App\Casts\EncryptedString;

protected $casts = [
    'tax_number' => EncryptedString::class,
    'phone' => EncryptedString::class,
    'mobile' => EncryptedString::class,
    'email' => EncryptedString::class,
    'iban' => EncryptedString::class,
];
```

**Nota:** Usa cast personalizado `EncryptedString` que cifra com `Crypt::encryptString()` sem serialização PHP, evitando prefixos `s:XX:` nos valores.

### 2. Contactos

**Model:** `App\Models\Contact`

| Campo    | Tipo   | Motivo                          |
| -------- | ------ | ------------------------------- |
| `phone`  | string | Telefone - dado pessoal (RGPD)  |
| `mobile` | string | Telemóvel - dado pessoal (RGPD) |
| `email`  | string | Email - dado pessoal (RGPD)     |

**Implementação:**

```php
use App\Casts\EncryptedString;

protected $casts = [
    'phone' => EncryptedString::class,
    'mobile' => EncryptedString::class,
    'email' => EncryptedString::class,
];
```

### 3. Utilizadores

**Model:** `App\Models\User`

| Campo      | Tipo   | Motivo                   | Nota                    |
| ---------- | ------ | ------------------------ | ----------------------- |
| `email`    | string | **NÃO CIFRADO**          | Usado para autenticação |
| `mobile`   | string | Telemóvel - dado pessoal | Cifrado                 |
| `password` | string | **HASHED**               | Usa bcrypt (não cifra)  |

**Implementação:**

```php
use App\Casts\EncryptedString;

protected function casts(): array
{
    return [
        'password' => 'hashed',  // bcrypt, não reversível
        'mobile' => EncryptedString::class,  // AES-256, reversível, sem serialização
    ];
}
```

⚠️ **Nota**: O email do utilizador NÃO é cifrado porque é usado como username no login. Passwords usam `hashed` (bcrypt) em vez de `encrypted` porque não precisam ser lidos.

### 4. Contas Bancárias

**Model:** `App\Models\BankAccount`

| Campo       | Tipo   | Motivo                              |
| ----------- | ------ | ----------------------------------- |
| `iban`      | string | IBAN - dado bancário muito sensível |
| `swift_bic` | string | SWIFT/BIC - código bancário         |

**Implementação:**

```php
use App\Casts\EncryptedString;

protected $casts = [
    'iban' => EncryptedString::class,
    'swift_bic' => EncryptedString::class,
];
```

## Cast Personalizado — EncryptedString

Para evitar serialização PHP (que adiciona prefixos `s:XX:` aos valores), foi criado um cast personalizado:

**Ficheiro:** `app/Casts/EncryptedString.php`

```php
<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EncryptedString implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            // Usa Crypt::decryptString() em vez de decrypt()
            // Não desserializa, retorna string pura
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        // Usa Crypt::encryptString() em vez de encrypt()
        // Não serializa, cifra a string diretamente
        return Crypt::encryptString($value);
    }
}
```

**Diferença entre `encrypted` e `EncryptedString`:**

| Cast                     | Método                                | Serialização           | Resultado                    |
| ------------------------ | ------------------------------------- | ---------------------- | ---------------------------- |
| `'encrypted'`            | `encrypt()` / `decrypt()`             | ✅ Sim (PHP serialize) | `s:19:"cliente@example.com"` |
| `EncryptedString::class` | `encryptString()` / `decryptString()` | ❌ Não                 | `cliente@example.com`        |

## Algoritmo de Cifra

### Especificações Técnicas

-   **Algoritmo:** AES-256-CBC (Advanced Encryption Standard)
-   **Tamanho da Chave:** 256 bits
-   **Modo:** CBC (Cipher Block Chaining)
-   **IV (Initialization Vector):** Gerado aleatoriamente para cada valor cifrado
-   **Autenticação:** HMAC-SHA256 para verificar integridade

### Estrutura do Dado Cifrado

O Laravel serializa o valor cifrado em JSON:

```json
{
    "iv": "base64_encoded_initialization_vector",
    "value": "base64_encoded_encrypted_value",
    "mac": "hmac_sha256_authentication_code",
    "tag": ""
}
```

Este JSON é depois convertido para base64 e guardado na base de dados.

## Impacto nas Queries

### ⚠️ Limitações Importantes

**1. Pesquisa por Campos Cifrados**

Não é possível fazer pesquisas diretas em campos cifrados:

```php
// ❌ NÃO FUNCIONA
Entity::where('email', 'cliente@example.com')->get();

// ✅ SOLUÇÃO: Pesquisar por campo não cifrado (name) e filtrar depois
$entities = Entity::all()->filter(function($entity) {
    return $entity->email === 'cliente@example.com';
});
```

**2. Ordenação**

Não é possível ordenar por campos cifrados diretamente na base de dados.

**3. Performance**

-   **Leitura:** Ligeiro overhead ao decifrar (microsegundos por campo)
-   **Escrita:** Ligeiro overhead ao cifrar (microsegundos por campo)
-   **Queries:** Sem impacto em WHERE/ORDER BY de campos não cifrados

### Estratégias de Pesquisa

Para manter funcionalidade de pesquisa:

1. **Usar campos não sensíveis para pesquisa:**

    - Pesquisar por `name` (não cifrado) em vez de `email` (cifrado)
    - Pesquisar por `number` (não cifrado) em vez de `tax_number` (cifrado)

2. **Filtrar em memória após carregar:**

    ```php
    $contacts = Contact::where('entity_id', $entityId)
        ->get()
        ->filter(fn($c) => str_contains($c->email, $search));
    ```

3. **Criar índices/tokens não cifrados:**
    - Para `email`: adicionar campo `email_hash` com hash do email
    - Pesquisar pelo hash em vez do valor real

## Verificação na Base de Dados

### Dados Cifrados (na BD)

```sql
mysql> SELECT email FROM entities LIMIT 1;
+-----------------------------------------------------------------------+
| email                                                                 |
+-----------------------------------------------------------------------+
| eyJpdiI6IkRxVGxKUzJhc1NRN0F4SHV6Z0R2UGc9PSIsInZhbHVlIjoiY2xpZW50... |
+-----------------------------------------------------------------------+
```

### Dados Decifrados (via Laravel)

```php
$entity = Entity::first();
echo $entity->email;
// Output: cliente@example.com
```

## Segurança da APP_KEY

### Geração

A chave é gerada automaticamente ao instalar o Laravel:

```bash
php artisan key:generate
```

### Proteção

1. **Nunca** commitar o `.env` no Git
2. **Backup seguro** da APP_KEY em local separado
3. **Rotação:** Evitar mudar a chave após dados cifrados
4. **Ambiente:** Chave diferente em dev/staging/production

### Backup da Chave

```bash
# Exportar chave atual
grep APP_KEY .env > .env.key.backup

# Guardar em local seguro (fora do repositório)
# Em caso de perda, dados cifrados são IRRECUPERÁVEIS
```

## Conformidade RGPD

A cifra de dados ajuda a cumprir:

-   **Artigo 32:** Segurança do tratamento de dados
-   **Artigo 5(1)(f):** Integridade e confidencialidade
-   **Princípio da Minimização:** Apenas dados necessários são guardados
-   **Direito ao Esquecimento:** Apagar cifra + chave = dados irrecuperáveis

## Dados NÃO Cifrados

Alguns campos permanecem em texto limpo por necessidade funcional:

### Campos de Negócio

-   `name`, `commercial_name` - Necessários para pesquisa e ordenação
-   `number` - Referência interna, não sensível
-   `country`, `city` - Dados geográficos não sensíveis
-   `website` - Informação pública
-   `notes` - Observações internas

### Campos Técnicos

-   `id`, `created_at`, `updated_at` - Metadados do sistema
-   `active`, `status` - Estados operacionais
-   Chaves estrangeiras e relacionamentos

### Campos Financeiros

-   Valores monetários (`credit_limit`, `saldo_atual`) - Necessários para cálculos SQL
-   Datas de transações - Necessárias para queries temporais

## Checklist de Implementação

-   [x] **Entity:** tax_number, phone, mobile, email, iban cifrados
-   [x] **Contact:** phone, mobile, email cifrados
-   [x] **User:** mobile cifrado (email não por ser usado em login)
-   [x] **BankAccount:** iban, swift_bic cifrados
-   [x] **APP_KEY** gerada e segura
-   [x] **Documentação** completa
-   [ ] **Testes** de cifra/decifra automática
-   [ ] **Backup** da APP_KEY em local seguro
-   [ ] **Migração** de dados existentes (se necessário)

## Migração de Dados Existentes

Se já existem dados na base de dados **antes** de ativar a cifra:

### Opção 1: Script de Migração (Recomendado)

```php
// scripts/encrypt_existing_data.php
<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Entity;
use App\Models\Contact;
use App\Models\BankAccount;
use App\Models\User;

// Entities
Entity::chunk(100, function ($entities) {
    foreach ($entities as $entity) {
        $entity->update([
            'tax_number' => $entity->getRawOriginal('tax_number'),
            'phone' => $entity->getRawOriginal('phone'),
            'mobile' => $entity->getRawOriginal('mobile'),
            'email' => $entity->getRawOriginal('email'),
            'iban' => $entity->getRawOriginal('iban'),
        ]);
    }
});

echo "✅ Dados migrados e cifrados com sucesso!\n";
```

### Opção 2: Base de Dados Limpa

Se ainda em desenvolvimento, resetar a base de dados:

```bash
php artisan migrate:fresh --seed
```

## Troubleshooting

### Erro: "The MAC is invalid"

**Causa:** APP_KEY foi alterada após cifrar dados

**Solução:** Restaurar APP_KEY original do backup

### Erro ao Pesquisar

**Causa:** Tentativa de WHERE/LIKE em campo cifrado

**Solução:** Usar estratégias de pesquisa alternativas (ver secção acima)

### Performance Lenta

**Causa:** Decifrar muitos registos simultaneamente

**Solução:**

-   Usar paginação
-   Carregar apenas campos necessários
-   Criar campos hash para pesquisa

## Conclusão

A implementação de cifra de dados sensíveis:

✅ **Protege** dados pessoais contra acesso não autorizado  
✅ **Cumpre** requisitos do RGPD  
✅ **Transparente** para o código da aplicação  
✅ **Automático** - sem necessidade de cifrar/decifrar manualmente  
✅ **Seguro** - usa AES-256-CBC, padrão da indústria

⚠️ **Atenção:** Fazer backup seguro da APP_KEY antes de deploy em produção!

---

**Última Atualização:** 18 Novembro 2025  
**Versão:** v0.25.0
