# Nova Imóveis — CRM + Site de Divulgação para Imobiliária

Sistema operacional imobiliário com site público de divulgação de imóveis e um CRM
administrativo completo, com arquitetura de dados inspirada na especificação **ALTIUS ONE**
(multiunidade, separação Contato/Lead/Oportunidade/Negócio/Transação, captação, propostas
versionadas, motor de comissões com rateio, auditoria e LGPD básico).

Construído com **Laravel 11 (PHP)**, **MySQL** (SQLite em desenvolvimento), **Tailwind CSS**,
**Livewire + Alpine.js** e mapas gratuitos com **Leaflet + OpenStreetMap** (sem custo de API).

## Modelo de dados (arquitetura ALTIUS)

```
ORGANIZATION -> UNIT -> TEAM -> USER
CONTACT -> LEAD -> OPPORTUNITY -> VISIT -> PROPOSAL -> DEAL -> COMMISSION_EVENT
CONTACT(owner) -> PROPERTY -> LISTING_AGREEMENT (captação)
```

- **Organization / Unit / Team**: estrutura multiunidade desde a base — toda entidade relevante
  carrega `organization_id` (e `unit_id` quando aplicável), preparando o sistema para operar
  mais de uma filial/franquia sem redesenho do banco.
- **Contact**: registro mestre de pessoas/empresas (compradores, proprietários, indicadores),
  com endereços, relacionamentos (cônjuge, sócio, procurador...), tags e timeline única.
- **Lead → Opportunity → Deal**: entidades distintas com ciclo de vida próprio — um Lead pode
  virar uma Oportunidade (buyer profile: orçamento, urgência, financiamento) e, ao aceitar uma
  Proposta, um Negócio (Deal Room) é criado.
- **Property + PropertyOwner + ListingAgreement**: o imóvel tem proprietário(s) (com percentual
  de titularidade) e um contrato de captação (aberta/exclusiva/assinatura) com pipeline próprio.
- **Proposal**: propostas **versionadas e imutáveis** — cada contraproposta cria uma nova versão
  (nunca edita a anterior), preservando histórico de negociação.
- **Deal + CommissionEvent + CommissionSplit**: ao fechar um negócio como ganho, o sistema gera
  automaticamente o evento de comissão e o rateio (split) entre captador, corretor e empresa,
  conforme o plano de comissão configurado.
- **AuditLog**: eventos sensíveis (alteração de preço, transferência de lead, aprovação de
  comissão, login/logout) ficam registrados com usuário, IP e valores antes/depois.
- **Consent / PrivacyRequest**: base de LGPD — consentimento por finalidade e canal, e fila de
  solicitações do titular (acesso, exclusão, correção, portabilidade).

## Funcionalidades

### Site público
- Home com busca rápida, imóveis em destaque e últimos anúncios
- Listagem com filtros (finalidade, tipo, cidade, bairro, preço, quartos) e ficha de imóvel
  (galeria, mapa Leaflet/OpenStreetMap, WhatsApp, formulário de contato)
- Cadastro/login de clientes — cada envio de formulário ou registro cria automaticamente um
  **Contact** vinculado, mantendo o histórico do cliente unificado desde o primeiro contato

### Área do cliente
- Favoritar imóveis, salvar buscas (com alerta automático por e-mail de novos imóveis
  compatíveis) e acompanhar visitas/contatos enviados

### PWA — instalável no celular
- Site público e CRM são **Progressive Web Apps** independentes (manifests separados):
  instale "Nova Imóveis" (cliente/site) ou "Nova Imóveis CRM" (equipe) na tela inicial do
  celular como um app nativo, com ícone, splash screen e sem a barra de endereço do navegador
- Botão **"Instalar app no celular"** no menu do CRM (aparece quando o navegador permite a
  instalação) e suporte a "Adicionar à tela inicial" no Safari/iOS
- Service worker com cache de assets estáticos e página de fallback offline — nunca armazena
  em cache HTML dinâmico (evita tokens CSRF/sessão desatualizados)

### CRM / Painel administrativo (`/admin`)
- **Dashboard** com indicadores por organização (imóveis ativos, leads no mês, SLA estourado,
  negócios fechados, comissões do mês) e gráficos de leads captados e negócios fechados nos
  últimos 6 meses
- **Contatos**: ficha 360° (leads, oportunidades, imóveis como proprietário, consentimentos,
  documentos, relacionamentos)
- **Leads / Funil (Kanban com arrastar-e-soltar)**: pipeline `NEW → ATTEMPTING_CONTACT →
  CONTACTED → QUALIFYING → QUALIFIED → OPPORTUNITY`, com saídas `NURTURE/LOST/SPAM/
  DUPLICATE/INVALID` (motivo de perda obrigatório), conversão explícita para Oportunidade
- **SLA automático com escalonamento**: distribuição de novos leads por carga (corretor com
  menos leads nas últimas 24h) e, para leads sem primeira resposta, cascata automática de
  lembrete (T+3/T+5 min) → escalonamento ao admin (T+10 min) → redistribuição para outro
  corretor (T+15 min), tudo registrado em auditoria
- **Oportunidades**: buyer profile (orçamento, urgência, financiamento, FGTS), critérios de
  busca estruturados, propostas em thread versionado (aceitar / rejeitar / contrapropor) e
  criação de negócio a partir da proposta aceita
- **ALTIUS Match**: score ponderado (0–100%) entre oportunidade e imóvel — preço 25%,
  localização 20%, tipologia 15%, dormitórios 10%, área 10%, vagas 5%, características 10%,
  preferências 5% — explicável (mostra o motivo de cada pontuação) e com busca reversa
  (a partir de um imóvel, veja quais compradores combinam com ele)
- **Imóveis**: CRUD completo, upload de fotos, características, localização no mapa
- **Captação**: contrato de captação (aberta/exclusiva/assinatura) com pipeline próprio
  (prospecção → ativa) e vínculo com o(s) proprietário(s) do imóvel
- **Negócios (Deal Room)**: abas de resumo, partes, imóvel, documentos, checklist e comissões;
  ao fechar como ganho, gera automaticamente o evento de comissão
- **Visitas**: agenda vinculada a imóveis, leads/oportunidades e corretores
- **Comissões**: motor com plano/regras/eventos/splits/pagamentos — rateio configurável por
  dimensão (captador, corretor, equipe, empresa...), aprovação, marcação de pago e exportação CSV
- **Documentos**: upload/download/exclusão de arquivos vinculados a leads e negócios, com
  armazenamento privado
- **Auditoria**: log de eventos sensíveis (preço, transferência de lead, comissão, login)
- **Privacidade (LGPD)**: fila de solicitações do titular com status e responsável
- **Exportação para portais**: feeds XML (padrão ZAP/VivaReal e um formato simplificado
  compatível com OLX/integradores)
- **Usuários e Configurações**: papéis, permissões e identidade visual do site

### Papéis de acesso (Spatie Permission)
| Papel | Escopo principal |
|---|---|
| **Admin** | Acesso total ao sistema e à organização |
| **Corretor** | Seus próprios leads, oportunidades, propostas, negócios e visitas |
| **Captador** | Pipeline de captação (proprietários e contratos de captação) |
| **Financeiro** | Negócios, comissões (aprovação e pagamento) |
| **Compliance** | Auditoria e Central de Privacidade (LGPD) |
| **Cliente** | Área do cliente (favoritos, buscas salvas, visitas) |

> Este é um subconjunto pragmático dos ~13 perfis descritos na especificação ALTIUS ONE
> (`SUPER_ADMIN`, `CEO/DIRETOR`, `GERENTE_UNIDADE`, `TEAM_LEADER`, `SDR`, `MARKETING`, etc.).
> A estrutura de Organization/Unit/Team já existe no banco para suportar papéis por unidade
> quando isso for necessário — hoje as permissões são globais por papel.

## Requisitos

- PHP >= 8.2 com extensões: `pdo_mysql` (ou `pdo_sqlite` em dev), `mbstring`, `gd`, `fileinfo`
- Composer 2.x
- Node.js 18+ e NPM (apenas para compilar os assets)
- MySQL 5.7+/MariaDB (produção) — SQLite é usado por padrão em desenvolvimento

## Instalação (desenvolvimento local)

```bash
composer install
npm install && npm run build
cp .env.example .env   # ajuste as variáveis se necessário
php artisan key:generate
touch database/database.sqlite   # se estiver usando SQLite
php artisan migrate --seed
php artisan storage:link
npm run dev   # ou: php artisan serve
```

### Usuários de demonstração (criados pelo seeder)

| Papel | E-mail | Senha |
|---|---|---|
| Admin | admin@novaimoveis.com.br | senha123 |
| Corretor | carla@novaimoveis.com.br | senha123 |
| Corretor | rafael@novaimoveis.com.br | senha123 |
| Captador | captador@novaimoveis.com.br | senha123 |
| Financeiro | financeiro@novaimoveis.com.br | senha123 |
| Compliance | compliance@novaimoveis.com.br | senha123 |
| Cliente | cliente@example.com | senha123 |

**Importante:** troque todas as senhas antes de colocar o sistema em produção.

## Deploy na Hostinger

### Opção rápida: instalador automático

Depois de criar o banco de dados MySQL (passo 1 abaixo) e conectar via SSH, você pode
rodar o instalador automático em vez de seguir os comandos manuais um a um. Ele detecta o
PHP disponível, baixa o Composer se precisar, clona/atualiza o projeto, cria o `.env`
perguntando só os dados essenciais, instala tudo, roda as migrations e (se você quiser) o
seed de demonstração, e ainda mostra o comando exato do cron job que falta configurar.

```bash
curl -o install-hostinger.sh -sSL https://raw.githubusercontent.com/tallentwave/imobiliariaCRM/claude/vibrant-pascal-w5ve97/deploy/install-hostinger.sh
less install-hostinger.sh   # dê uma olhada no script antes de rodar, é sempre bom hábito
bash install-hostinger.sh
```

Se preferir, pode passar uma pasta de destino diferente: `bash install-hostinger.sh /home/seu-usuario/nova-imoveis`.
Ele pode ser executado mais de uma vez sem problema (é seguro rodar de novo para atualizar).

Se o servidor não tiver Node.js (comum em hospedagem compartilhada), o script avisa e você
deve rodar `npm install && npm run build` na sua máquina e enviar a pasta `public/build`
pronta antes de rodar o instalador de novo.

O restante desta seção descreve os mesmos passos manualmente, caso prefira ter controle
total de cada etapa ou precise adaptar algo específico do seu plano de hospedagem.

0. **Pré-requisitos no hPanel** (antes de qualquer coisa):
   - **Versão do PHP**: em "Avançado" → "Configuração do PHP", selecione **PHP 8.2 ou superior**
     (planos Hostinger costumam vir com uma versão antiga por padrão — o Laravel 11 não sobe com
     PHP < 8.2).
   - **Acesso SSH**: em "Avançado" → "Acesso SSH", ative o acesso e anote host/porta/usuário.
     Sem SSH você consegue rodar `composer install` e `php artisan` pelo terminal do hPanel em
     alguns planos, mas SSH é mais confiável para os comandos abaixo.
   - **Node.js**: o build do front-end (`npm run build`) normalmente precisa ser feito **fora**
     do servidor (ambiente local ou CI), pois hospedagem compartilhada raramente tem Node.js
     disponível — depois é só enviar a pasta `public/build` já gerada junto com o restante dos
     arquivos.
1. **Banco de dados MySQL**: no hPanel, crie um banco de dados MySQL e um usuário. Anote
   nome do banco, usuário, senha e host (geralmente `localhost`).
2. **Enviar os arquivos**: envie todo o projeto para o servidor (via Git, FTP ou o Gerenciador
   de Arquivos). O **document root** do domínio deve apontar para a pasta `public/` do projeto
   (na Hostinger isso é configurado em "Domínios" → "Gerenciar" → alterar a pasta raiz do site).
3. **Configurar o `.env`**: copie `.env.example` para `.env` e preencha:
   - `APP_URL` com a URL final do site
   - `APP_ENV=production` e `APP_DEBUG=false`
   - Dados do banco MySQL (`DB_*`)
   - Dados de e-mail SMTP (`MAIL_*`) — a Hostinger fornece um SMTP próprio no hPanel
4. **Instalar dependências e preparar a aplicação** (via SSH ou terminal do hPanel):
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build   # pule esta linha se já enviou a pasta public/build pronta (item 0)
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force   # opcional: cria organização, unidade e usuário admin inicial
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   Se o comando `composer` do servidor apontar para uma versão antiga do PHP, use
   `php8.2 /usr/local/bin/composer install ...` (ou o binário indicado pelo suporte da Hostinger
   para a versão do PHP escolhida no item 0).
5. **Permissões**: garanta que as pastas `storage/` e `bootstrap/cache/` tenham permissão de
   escrita para o servidor web.
6. **Agendador de tarefas (cron)**: para os alertas de buscas salvas e o SLA automático de
   leads funcionarem, configure na Hostinger (hPanel → "Avançado" → "Cron Jobs") uma tarefa
   que rode a cada minuto:
   ```
   * * * * * php /caminho/do/projeto/artisan schedule:run >> /dev/null 2>&1
   ```
   Isso dispara `app:process-lead-sla` (a cada minuto) e `app:notify-saved-searches`
   (diariamente às 8h) automaticamente — não é preciso configurar cada comando separadamente.
7. Acesse `/login` com o usuário admin criado pelo seeder e **troque a senha imediatamente**
   em "Meu perfil".

## Exportação para portais imobiliários

Em **Admin → Exportar p/ portais** ficam disponíveis dois feeds XML gerados dinamicamente:

- `/admin/exportar/zap-vivareal.xml` — estrutura no padrão ZAP/VivaReal (`ListingDataFeed`)
- `/admin/exportar/olx.xml` — formato simplificado compatível com integradores de anúncios

⚠️ Os portais podem alterar a especificação exigida sem aviso prévio — **confirme os nomes
exatos de campo com o suporte técnico do portal** antes de ativar o envio automático em produção.

## Aderência à especificação ALTIUS ONE

A especificação ALTIUS ONE descreve um sistema operacional imobiliário de escala
enterprise (multiunidade, ~13 perfis, motor de match, compliance/PLD-FT, BI avançado, Academy,
IA). Implementar tudo é um esforço de meses de uma equipe — este projeto prioriza o **núcleo
transacional e comercial** (o que a própria especificação chama de V1.0) e deixa claro o que
ainda não foi construído:

**Implementado nesta fase:**
Identity/tenancy (Organization/Unit/Team), Contatos, Leads com pipeline completo, SLA com
escalonamento automático (T+3/T+5/T+10/T+15) e distribuição por carga entre corretores,
Oportunidades (buyer profile), ALTIUS Match (score ponderado explicável, com busca reversa),
Captação com proprietários, Propostas versionadas, Deal Room com checklist, Motor de
comissões com splits, Auditoria de eventos sensíveis, LGPD básico (consentimento + fila de
solicitações), Documentos, Visitas, Exportação para portais, Dashboard com gráficos, PWA
instalável (site e CRM).

**Não implementado (fora de escopo desta fase — corresponde a V1.5/V2/V3 no roadmap da
especificação, ou depende de serviços externos que exigem credenciais próprias):**
- Assinatura eletrônica de contratos (ex.: D4Sign/Clicksign) — requer conta/API key do provedor
- WhatsApp Business API oficial (histórico de conversas dentro do CRM) — hoje só há botão de
  link direto (`wa.me`); a API oficial exige conta Meta Business aprovada
- Distribuição de leads por especialidade/região (hoje é só por carga/round-robin simples)
- Compliance/PLD-FT (KYC, risk flags, casos confidenciais)
- Owner Portal dedicado, ALTIUS Academy (cursos/certificações), Score e carreira
- BI avançado (funil de conversão, CAC/ROAS por campanha), mapa de inteligência de mercado,
  ALTIUS AI
- API pública com OpenAPI/Swagger, webhooks e automações configuráveis (WHEN/IF/THEN)
- Permissões de campo (ex.: mascarar CPF) e de ação (ex.: exportação negada mesmo com leitura)
- Multiunidade operacional completa (o schema já suporta, mas hoje há uma única organização/
  unidade em uso e os papéis não são segregados por unidade)

## Estrutura de dados (principais tabelas)

- `organizations`, `units`, `teams` — estrutura multiunidade
- `contacts`, `contact_addresses`, `contact_relationships`, `tags` — registro mestre de pessoas
- `properties`, `property_images`, `property_owners`, `listing_agreements`, `features`
- `leads`, `opportunities`, `opportunity_requirements`
- `proposals` (versionadas), `deals`, `deal_parties`, `deal_checklists`
- `commission_plans`, `commission_rules`, `commission_events`, `commission_splits`,
  `commission_payments`
- `visits`, `favorites`, `saved_searches`, `documents`, `settings`
- `audit_logs`, `consents`, `privacy_requests`
- `users` + tabelas do Spatie Permission

Arquivos do PWA: `public/manifest.webmanifest` (site), `public/admin-manifest.webmanifest`
(CRM), `public/sw.js` (service worker), `public/offline.html` (fallback offline),
`public/icons/` (ícones gerados).

## Comandos úteis

```bash
php artisan migrate:fresh --seed   # recria o banco com dados de demonstração
php artisan tinker                 # console interativo
npm run dev                        # build de assets com hot-reload (desenvolvimento)
npm run build                      # build de assets para produção
```
