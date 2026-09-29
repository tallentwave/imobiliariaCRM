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
- **Funil de vendas (Kanban)**: leads organizados por etapa (novo → em contato → visita →
  proposta → fechado ganho/perdido)
- **Visitas**: agenda de visitas vinculadas a imóveis, leads e corretores
- **Usuários**: gestão de administradores, corretores e financeiro, com papéis e permissões
- **Comissões**: valor negociado e percentual de comissão calculados automaticamente por lead
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
6. Acesse `/login` com o usuário admin criado pelo seeder e **troque a senha imediatamente**
   em "Meu perfil".

## Estrutura de dados (principais tabelas)

- `properties` — imóveis (preço, tipo, finalidade, endereço, status, corretor responsável)
- `property_images` — fotos de cada imóvel
- `features` — comodidades (piscina, academia, etc.) associadas aos imóveis
- `leads` — contatos recebidos, com etapa do funil, valor negociado e comissão
- `visits` — agenda de visitas
- `favorites` / `saved_searches` — dados da área do cliente
- `settings` — configurações gerais do site (nome, logo, contatos, redes sociais)
- `users` + tabelas do Spatie Permission — usuários, papéis e permissões

## Roadmap sugerido (próximas etapas)

Este é um sistema completo e funcional (fase 1). Para evoluir ainda mais, considere:

- [ ] Exportação de feed XML para portais (Zap Imóveis, OLX, VivaReal)
- [ ] Kanban com arrastar-e-soltar (Livewire, já incluso no projeto) em vez de seleção manual
- [ ] Upload e assinatura de documentos/contratos por negócio
- [ ] Envio automático de e-mail para buscas salvas quando novos imóveis correspondem aos filtros
- [ ] Relatórios financeiros mais detalhados (comissões por corretor, por período)
- [ ] Integração com WhatsApp Business API para histórico de conversas dentro do CRM
- [ ] App mobile ou PWA para corretores em campo

## Comandos úteis

```bash
php artisan migrate:fresh --seed   # recria o banco com dados de demonstração
php artisan tinker                 # console interativo
npm run dev                        # build de assets com hot-reload (desenvolvimento)
npm run build                      # build de assets para produção
```
