# Nova Imóveis — CRM + Site de Divulgação para Imobiliária

Sistema completo para imobiliárias, com site público de divulgação de imóveis e um CRM
administrativo para gestão de imóveis, leads (funil de vendas), visitas, comissões e usuários.

Construído com **Laravel 11 (PHP)**, **MySQL** (SQLite em desenvolvimento), **Tailwind CSS**,
**Alpine.js** e mapas gratuitos com **Leaflet + OpenStreetMap** (sem custo de API).

## Funcionalidades

### Site público
- Página inicial com busca rápida, imóveis em destaque e últimos anúncios
- Listagem de imóveis com filtros (finalidade, tipo, cidade, bairro, preço, quartos)
- Página de detalhe do imóvel: galeria de fotos, mapa de localização, características,
  corretor responsável, botão de WhatsApp e formulário de contato
- Páginas institucionais (Sobre, Contato)
- Cadastro/login de clientes

### Área do cliente (usuário logado)
- Favoritar imóveis
- Salvar buscas (filtros) para receber novidades
- Acompanhar visitas agendadas e contatos enviados

### CRM / Painel administrativo (`/admin`)
- **Dashboard** com indicadores (imóveis ativos, leads no mês, negócios fechados, comissões)
- **Imóveis**: CRUD completo, upload de múltiplas fotos, características, localização no mapa
- **Funil de vendas (Kanban com arrastar-e-soltar)**: leads organizados por etapa (novo → em
  contato → visita → proposta → fechado ganho/perdido), construído com Livewire + Sortable.js
- **Documentos e contratos**: upload/download/exclusão de arquivos (PDF, Word, imagens)
  vinculados a cada lead/negócio, com armazenamento privado
- **Visitas**: agenda de visitas vinculadas a imóveis, leads e corretores
- **Usuários**: gestão de administradores, corretores e financeiro, com papéis e permissões
- **Comissões e relatórios financeiros**: valor negociado e comissão calculados
  automaticamente por lead, relatório por corretor/período com exportação em CSV e
  marcação de comissão paga/pendente
- **Exportação para portais**: feeds XML dinâmicos (padrão ZAP/VivaReal e um formato
  simplificado compatível com OLX/integradores) para publicação automática dos imóveis
- **Alertas automáticos por e-mail**: clientes que salvarem uma busca recebem e-mail quando
  novos imóveis publicados combinarem com os filtros salvos (via agendador do Laravel)
- **Configurações**: nome, logo, cores, WhatsApp, redes sociais, endereço

### Papéis de acesso (Spatie Permission)
| Papel | Acesso |
|---|---|
| **Admin** | Acesso total ao sistema |
| **Corretor** | Seus próprios imóveis e leads, agenda de visitas |
| **Financeiro** | Visualização de leads e gestão de comissões |
| **Cliente** | Área do cliente (favoritos, buscas salvas, visitas) |

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
| Financeiro | financeiro@novaimoveis.com.br | senha123 |
| Cliente | cliente@example.com | senha123 |

**Importante:** troque todas as senhas antes de colocar o sistema em produção.

## Deploy na Hostinger

1. **Banco de dados MySQL**: no hPanel, crie um banco de dados MySQL e um usuário. Anote
   nome do banco, usuário, senha e host (geralmente `localhost`).
2. **Enviar os arquivos**: envie todo o projeto para o servidor (via Git, FTP ou o Gerenciador
   de Arquivos). O **document root** do domínio deve apontar para a pasta `public/` do projeto
   (na Hostinger isso é configurado em "Domínios" → "Gerenciar" → alterar a pasta raiz do site).
3. **Configurar o `.env`**: copie `.env.example` para `.env` e preencha:
   - `APP_URL` com a URL final do site
   - `APP_ENV=production` e `APP_DEBUG=false`
   - Dados do banco MySQL (`DB_*`)
   - `WHATSAPP_NUMBER` com o número da imobiliária
   - Dados de e-mail SMTP (`MAIL_*`) — a Hostinger fornece um SMTP próprio no hPanel
4. **Instalar dependências e preparar a aplicação** (via SSH ou terminal do hPanel):
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   php artisan key:generate
   php artisan migrate --force
   php artisan db:seed --force   # opcional: cria o usuário admin inicial
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. **Permissões**: garanta que as pastas `storage/` e `bootstrap/cache/` tenham permissão de
   escrita para o servidor web.
6. **Agendador de tarefas (cron)**: para os alertas automáticos de buscas salvas funcionarem,
   configure na Hostinger (hPanel → "Avançado" → "Cron Jobs") uma tarefa que rode a cada
   minuto:
   ```
   * * * * * php /caminho/do/projeto/artisan schedule:run >> /dev/null 2>&1
   ```
7. Acesse `/login` com o usuário admin criado pelo seeder e **troque a senha imediatamente**
   em "Meu perfil".

## Estrutura de dados (principais tabelas)

- `properties` — imóveis (preço, tipo, finalidade, endereço, status, corretor responsável)
- `property_images` — fotos de cada imóvel
- `features` — comodidades (piscina, academia, etc.) associadas aos imóveis
- `leads` — contatos recebidos, com etapa do funil, valor negociado e comissão
- `visits` — agenda de visitas
- `favorites` / `saved_searches` — dados da área do cliente
- `documents` — arquivos/contratos vinculados a leads (armazenamento privado)
- `settings` — configurações gerais do site (nome, logo, contatos, redes sociais)
- `users` + tabelas do Spatie Permission — usuários, papéis e permissões

## Exportação para portais imobiliários

Em **Admin → Exportar p/ portais** ficam disponíveis dois feeds XML gerados dinamicamente a
partir dos imóveis com status "Disponível"/"Reservado":

- `/admin/exportar/zap-vivareal.xml` — estrutura no padrão ZAP/VivaReal (`ListingDataFeed`)
- `/admin/exportar/olx.xml` — formato simplificado compatível com integradores de anúncios

⚠️ Os portais podem alterar a especificação exigida sem aviso prévio — **confirme os nomes
exatos de campo com o suporte técnico do portal** antes de ativar o envio automático em
produção.

## Roadmap sugerido (próximas etapas)

Este sistema já cobre um CRM completo e funcional. Para evoluir ainda mais, considere:

- [ ] Assinatura eletrônica de contratos (ex.: integração com D4Sign/Clicksign)
- [ ] Integração com WhatsApp Business API para histórico de conversas dentro do CRM
- [ ] Envio automático dos feeds XML diretamente para a API de cada portal (hoje a URL do
      feed precisa ser cadastrada manualmente no painel do portal)
- [ ] Gráficos no dashboard (evolução de leads e vendas ao longo do tempo)
- [ ] App mobile ou PWA para corretores em campo

## Comandos úteis

```bash
php artisan migrate:fresh --seed   # recria o banco com dados de demonstração
php artisan tinker                 # console interativo
npm run dev                        # build de assets com hot-reload (desenvolvimento)
npm run build                      # build de assets para produção
```
