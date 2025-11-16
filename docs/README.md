# 📚 Documentação do Projeto Gest-App

## ⚠️ ATENÇÃO - Segurança da Documentação

Este projeto tem um **repositório público no GitHub**. Por isso, alguns documentos NÃO devem ser commitados.

---

## ✅ Documentos PÚBLICOS (podem ir para GitHub)

Estes documentos estão seguros para commit:

-   ✅ `README.md` - Documentação geral do projeto
-   ✅ `changelog.md` - Histórico de versões (sem informação sensível)
-   ✅ `compliance-check.md` - Verificação de requisitos (genérico)
-   ✅ `modular-architecture.md` - Arquitetura do projeto
-   ✅ `*.module.md` - Documentação de módulos individuais
-   ✅ Este ficheiro (`docs/README.md`)

---

## 🔒 Documentos PRIVADOS (NÃO fazer commit)

Estes documentos contêm informação sensível e estão no `.gitignore`:

### 🔑 Segurança e Chaves

-   ❌ `APP_KEY-BACKUP-GUIDE.md` - **Contém APP_KEY real**
-   ❌ `security-implementation.md` - Detalhes de implementação
-   ❌ `security-summary.md` - Resumo de segurança
-   ❌ `security-summary-final.md` - Estado final de segurança

### 📊 Relatórios Internos

-   ❌ `relatorio-progresso.md` - Relatório de estágio (informação interna)

---

## 📋 Checklist Antes de Commit

Antes de fazer `git push`, verifica sempre:

```bash
# Ver ficheiros que vão ser commitados
git status

# Verificar se não há ficheiros sensíveis
git status | Select-String "security|APP_KEY|relatorio"
```

**Se aparecer algum ficheiro de segurança:**

```bash
# NÃO fazer commit! Remover do staging:
git reset HEAD docs/security-*.md
git reset HEAD docs/APP_KEY-BACKUP-GUIDE.md
```

---

## 🛡️ Proteções Implementadas

### .gitignore configurado:

```gitignore
# Documentos de segurança sensíveis (repositório público)
docs/APP_KEY-BACKUP-GUIDE.md
docs/security-implementation.md
docs/security-summary.md
docs/security-summary-final.md

# Relatórios internos
relatorio-progresso.md
```

### Avisos nos documentos:

Todos os documentos privados têm aviso no topo:

```markdown
> ⚠️ DOCUMENTO CONFIDENCIAL - NÃO FAZER COMMIT NO GIT
```

---

## 💡 Boas Práticas

### ✅ O que fazer:

1. Manter documentação pública atualizada
2. Remover informação sensível antes de commit
3. Usar exemplos genéricos em docs públicos
4. Verificar `git status` antes de cada commit

### ❌ O que NÃO fazer:

1. Commitar ficheiros com APP_KEY
2. Incluir passwords ou secrets em docs
3. Partilhar detalhes de implementação de segurança
4. Fazer commit de relatórios internos/confidenciais

---

## 📋 Configurações de Sistema Necessárias

### PHP Configuration (php.ini)

Para suportar uploads de ficheiros até 5MB (faturas, documentos):

```ini
upload_max_filesize = 10M
post_max_size = 10M
```

**Localização do php.ini (Herd):**

-   Windows: `C:\Users\{username}\.config\herd\bin\php83\php.ini`

**Após alterar, reiniciar servidor:**

```bash
herd restart
```

---

## 📞 Em caso de Erro

**Se commitaste acidentalmente um ficheiro sensível:**

```bash
# 1. Remover do último commit (antes de push)
git reset HEAD~1
git restore --staged docs/APP_KEY-BACKUP-GUIDE.md

# 2. Se já fizeste push (URGENTE)
# Contactar responsável do projeto imediatamente
# Pode ser necessário:
# - Regenerar APP_KEY (⚠️ requer re-encriptação de dados)
# - Fazer force push para remover do histórico
# - Invalidar secrets expostos
```

---

## 🎯 Resumo

| Ficheiro                  | Público? | Razão                     |
| ------------------------- | -------- | ------------------------- |
| `changelog.md`            | ✅ SIM   | Histórico genérico        |
| `compliance-check.md`     | ✅ SIM   | Requisitos (sem secrets)  |
| `APP_KEY-BACKUP-GUIDE.md` | ❌ NÃO   | **Contém APP_KEY real**   |
| `security-*.md`           | ❌ NÃO   | Detalhes de implementação |
| `relatorio-progresso.md`  | ❌ NÃO   | Informação interna        |

---

**Última atualização:** 16 Novembro 2025  
**Projeto:** Gest-App (INOVCORP)  
**Repositório:** Público (github.com/JoseGInovcorp/gest-app)
