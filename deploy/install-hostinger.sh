#!/usr/bin/env bash
#
# Instalador automático — Nova Imóveis CRM (Hostinger)
#
# O que este script faz, sozinho, via SSH:
#   1. Detecta um PHP >= 8.2 disponível no servidor
#   2. Baixa o Composer localmente se ele não existir
#   3. Clona (ou atualiza) o projeto no servidor
#   4. Cria o .env perguntando só os dados essenciais (domínio, banco, e-mail)
#   5. Instala as dependências PHP (composer install --no-dev)
#   6. Compila os assets de front-end (se Node.js existir) ou avisa para
#      enviar a pasta public/build já pronta, feita na sua máquina local
#   7. Gera a APP_KEY, roda as migrations e (opcional) o seed de demonstração
#   8. Cria o link de storage, ajusta permissões e gera o cache de produção
#   9. Mostra o comando exato do cron job que falta configurar no hPanel
#      (isso não dá pra automatizar por SSH — é feito na interface do hPanel)
#
# Uso (depois de conectar via SSH na Hostinger):
#   bash install-hostinger.sh [pasta-destino]
#
# Pode ser executado mais de uma vez sem problema: se o projeto e o .env já
# existirem, o script pula essas etapas e apenas atualiza/reinstala o resto.

set -euo pipefail

REPO_URL="${REPO_URL:-https://github.com/tallentwave/imobiliariaCRM.git}"
BRANCH="${BRANCH:-claude/vibrant-pascal-w5ve97}"
DEST="${1:-$HOME/imobiliariaCRM}"

c_ok()   { printf '\033[32m✔ %s\033[0m\n' "$1"; }
c_info() { printf '\033[36mℹ %s\033[0m\n' "$1"; }
c_warn() { printf '\033[33m⚠ %s\033[0m\n' "$1"; }
c_err()  { printf '\033[31m✘ %s\033[0m\n' "$1"; }

# 1) PHP >= 8.2 -----------------------------------------------------------
PHP_BIN=""
for candidate in php8.4 php8.3 php8.2 php; do
  if command -v "$candidate" >/dev/null 2>&1; then
    ver=$("$candidate" -r 'echo PHP_VERSION;' 2>/dev/null || echo "0.0.0")
    major=$(echo "$ver" | cut -d. -f1)
    minor=$(echo "$ver" | cut -d. -f2)
    if [ "$major" -gt 8 ] 2>/dev/null || { [ "$major" -eq 8 ] 2>/dev/null && [ "$minor" -ge 2 ] 2>/dev/null; }; then
      PHP_BIN=$(command -v "$candidate")
      break
    fi
  fi
done
if [ -z "$PHP_BIN" ]; then
  c_err "Nenhum PHP >= 8.2 encontrado no PATH."
  c_warn "No hPanel: Avançado > Configuração do PHP > selecione PHP 8.2 ou superior. Depois rode este script de novo."
  exit 1
fi
c_ok "PHP encontrado: $PHP_BIN ($("$PHP_BIN" -r 'echo PHP_VERSION;'))"

# 2) Composer --------------------------------------------------------------
COMPOSER_BIN=""
if command -v composer >/dev/null 2>&1; then
  COMPOSER_BIN="$(command -v composer)"
elif [ -f "$HOME/bin/composer" ]; then
  COMPOSER_BIN="$HOME/bin/composer"
else
  c_info "Composer não encontrado — baixando uma cópia local em ~/bin/composer..."
  mkdir -p "$HOME/bin"
  "$PHP_BIN" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  "$PHP_BIN" composer-setup.php --install-dir="$HOME/bin" --filename=composer
  rm -f composer-setup.php
  COMPOSER_BIN="$HOME/bin/composer"
fi
c_ok "Composer disponível em: $COMPOSER_BIN"

# 3) Código do projeto -------------------------------------------------------
if [ -d "$DEST/.git" ]; then
  c_info "Projeto já existe em $DEST — atualizando..."
  git -C "$DEST" fetch origin "$BRANCH"
  git -C "$DEST" checkout "$BRANCH"
  git -C "$DEST" pull origin "$BRANCH"
else
  c_info "Clonando o projeto em $DEST (branch $BRANCH)..."
  git clone --branch "$BRANCH" "$REPO_URL" "$DEST"
fi
cd "$DEST"
c_ok "Código pronto em $DEST"

# 4) .env ---------------------------------------------------------------
if [ ! -f .env ]; then
  cp .env.example .env
  c_info "Arquivo .env criado. Preencha os dados essenciais:"

  read -rp "  Domínio final do site (ex: https://www.novaimoveis.com.br): " APP_URL_VAL
  read -rp "  Nome do banco de dados MySQL (hPanel > Bancos de dados): " DB_NAME
  read -rp "  Usuário do banco de dados MySQL: " DB_USER
  read -rsp "  Senha do banco de dados MySQL: " DB_PASS
  echo
  read -rp "  Host do banco (Enter para manter 'localhost'): " DB_HOST_VAL
  DB_HOST_VAL="${DB_HOST_VAL:-localhost}"
  read -rp "  Host SMTP da Hostinger (Enter para configurar depois manualmente): " MAIL_HOST_VAL

  sed -i "s#^APP_URL=.*#APP_URL=${APP_URL_VAL}#" .env
  sed -i "s/^DB_HOST=.*/DB_HOST=${DB_HOST_VAL}/" .env
  sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
  sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
  sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env
  if [ -n "$MAIL_HOST_VAL" ]; then
    sed -i "s/^MAIL_HOST=.*/MAIL_HOST=${MAIL_HOST_VAL}/" .env
  fi
  c_ok ".env configurado."
else
  c_warn ".env já existe — mantendo como está (edite manualmente se precisar trocar banco/domínio)."
  APP_URL_VAL="$(grep '^APP_URL=' .env | cut -d= -f2-)"
fi

# 5) Dependências PHP -----------------------------------------------------
c_info "Instalando dependências PHP (composer install --no-dev)..."
"$PHP_BIN" "$COMPOSER_BIN" install --optimize-autoloader --no-dev --no-interaction
c_ok "Dependências PHP instaladas."

# 6) Assets de front-end ----------------------------------------------------
if [ -d public/build ] && [ -f public/build/manifest.json ]; then
  c_ok "Assets já compilados encontrados em public/build — pulando build de front-end."
elif command -v npm >/dev/null 2>&1; then
  c_info "Node.js encontrado, compilando assets..."
  npm ci
  npm run build
  c_ok "Assets compilados."
else
  c_err "public/build não existe e este servidor não tem Node.js (comum em hospedagem compartilhada)."
  c_warn "Rode 'npm install && npm run build' na sua máquina local, envie a pasta public/build para"
  c_warn "$DEST/public/build (via FTP/SFTP) e rode este script de novo."
  exit 1
fi

# 7) Chave da aplicação, migrations e seed -----------------------------------
if ! grep -q '^APP_KEY=base64' .env; then
  "$PHP_BIN" artisan key:generate --force
  c_ok "APP_KEY gerada."
fi

"$PHP_BIN" artisan migrate --force
c_ok "Migrations executadas."

read -rp "Deseja criar os dados de demonstração (organização, unidade e usuário admin inicial)? [s/N]: " SEED_ANSWER
if [[ "${SEED_ANSWER:-}" =~ ^[sS]$ ]]; then
  "$PHP_BIN" artisan db:seed --force
  c_ok "Seed executado — login inicial: admin@novaimoveis.com.br / senha123 (troque assim que entrar!)"
fi

# 8) Storage, permissões e cache de produção ---------------------------------
"$PHP_BIN" artisan storage:link || c_warn "storage:link já existia ou falhou (verifique manualmente)."

chmod -R 775 storage bootstrap/cache
c_ok "Permissões ajustadas em storage/ e bootstrap/cache/."

"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
c_ok "Cache de produção gerado."

# 9) Passo manual que falta: o cron job -----------------------------------
echo
c_info "Falta 1 passo manual — o hPanel não permite configurar isso via SSH:"
echo "  1. No hPanel, vá em Avançado > Cron Jobs"
echo "  2. Crie uma tarefa 'A cada minuto' com este comando:"
echo "     * * * * * $PHP_BIN $DEST/artisan schedule:run >> /dev/null 2>&1"
echo

c_ok "Instalação concluída!"
c_ok "Acesse: ${APP_URL_VAL:-<a URL configurada no .env>}/login"
c_warn "Troque todas as senhas padrão (senha123) imediatamente após o primeiro login."
