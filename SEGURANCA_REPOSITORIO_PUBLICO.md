# 🔐 CHECKLIST DE SEGURANÇA - REPOSITÓRIO PÚBLICO

**IMPORTANTE:** Execute TODOS os passos antes de tornar o repositório público!

---

## ⚠️ PROBLEMA DETECTADO

O arquivo `.env` com credenciais sensíveis está no histórico do Git!

```
Credenciais expostas:
- DB_PASSWORD="Pandora@1989"
- MAIL_PASSWORD=Pandora@1989
- DB_HOST=srv1890.hstgr.io
- DB_DATABASE=u230868210_djsgqpro
- DB_USERNAME=u230868210_dusouza
```

---

## ✅ SOLUÇÃO - PASSO A PASSO

### 1️⃣ REMOVER .env DO HISTÓRICO

```bash
# Instalar BFG Repo Cleaner (mais rápido que git filter-branch)
# Baixar de: https://rtyley.github.io/bfg-repo-cleaner/

# OU usar git filter-branch:
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch .env" \
  --prune-empty --tag-name-filter cat -- --all
```

### 2️⃣ GARANTIR QUE .env ESTÁ NO .gitignore

```bash
# Verificar se .env está no .gitignore
cat .gitignore | findstr ".env"

# Se NÃO estiver, adicionar:
echo .env >> .gitignore
```

### 3️⃣ CRIAR .env.example (SEM CREDENCIAIS)

```bash
# Copiar estrutura sem valores sensíveis
copy .env .env.example

# Depois, editar .env.example e substituir valores reais por placeholders:
# DB_PASSWORD="sua_senha_aqui"
# MAIL_PASSWORD=sua_senha_aqui
```

### 4️⃣ FORÇAR PUSH (REESCREVER HISTÓRICO)

```bash
git push origin --force --all
git push origin --force --tags
```

### 5️⃣ **TROCAR TODAS AS SENHAS!**

Mesmo após limpar o histórico, você DEVE trocar:
- ✅ Senha do banco de dados
- ✅ Senha do email (SMTP)
- ✅ Qualquer outra credencial que estava no .env

**Por quê?** O histórico antigo pode ter sido clonado por alguém.

---

## 🚀 ALTERNATIVA MAIS SEGURA

### Opção 1: Criar Repositório Novo

```bash
# 1. Criar novo repositório público no GitHub
# 2. Adicionar .env ao .gitignore ANTES do primeiro commit
# 3. Push apenas dos arquivos seguros
```

### Opção 2: Usar Git Submodules para Credenciais

```bash
# Manter .env em repositório PRIVADO separado
# Referenciá-lo como submodule
```

---

## ✅ VERIFICAÇÃO FINAL

Antes de tornar público, verifique:

- [ ] .env está no .gitignore
- [ ] .env.example existe (sem credenciais reais)
- [ ] Histórico do Git não contém .env
- [ ] Todas as senhas foram trocadas
- [ ] Nenhum arquivo de backup contém credenciais
- [ ] Nenhum SQL dump com dados reais

---

## 🔒 BOAS PRÁTICAS PARA REPOSITÓRIO PÚBLICO

### Nunca Versionar:
- ❌ .env (credenciais)
- ❌ /vendor (dependências)
- ❌ /node_modules
- ❌ /storage/logs (podem conter dados sensíveis)
- ❌ /uploads (arquivos de usuários)
- ❌ SQL dumps com dados reais

### Sempre Incluir:
- ✅ .env.example (template sem credenciais)
- ✅ README.md (documentação)
- ✅ .gitignore (completo)
- ✅ composer.json
- ✅ package.json (se usar)

---

## 📝 .gitignore RECOMENDADO

```gitignore
# Ambiente
.env
.env.backup
.env.production

# Dependências
/vendor
/node_modules

# Storage e Uploads
/storage/*.key
/storage/logs/*.log
/uploads/*
!/uploads/.gitkeep

# IDEs e Editores
.vscode/
.idea/
*.swp
*.swo
*~

# Sistema Operacional
.DS_Store
Thumbs.db

# Temporários
*.tmp
*.cache
```

---

## 🆘 COMANDOS ÚTEIS

### Verificar se .env está no Git
```bash
git ls-files | findstr ".env"
```

### Verificar histórico de arquivo sensível
```bash
git log --all --full-history -- .env
```

### Ver status do .gitignore
```bash
git check-ignore -v .env
```

---

## ⏭️ PRÓXIMOS PASSOS

1. **Execute a limpeza do histórico**
2. **Troque TODAS as senhas**
3. **Verifique que nada sensível está no Git**
4. **Torne o repositório público via GitHub Settings**

---

**Preparado por:** Antigravity AI  
**Data:** 04/12/2025  
**Prioridade:** 🔴 CRÍTICA DE SEGURANÇA

