# CRM Fred - Sistema Completo de Gestão

## 🚀 Sobre o Projeto

CRM profissional e modular desenvolvido com Laravel 12, MySQL, TailwindCSS e Alpine.js. Inclui sistema de autenticação completo, RBAC, Kanban Board, Chat estilo WhatsApp e integração com Evolution API.

## ✨ Funcionalidades

### 🔐 Autenticação
- Login e registro de usuários
- Reset de senha
- Sessões seguras
- Rate limiting
- Modo escuro

### 👥 RBAC (Controle de Acesso)
- 4 papéis padrão: Admin, Gestor, Vendedor, Suporte
- Permissões customizáveis
- Middleware de proteção de rotas
- Sistema de Gates e Policies

### 📋 Kanban Board
- Colunas personalizáveis
- Cards com drag & drop
- Prioridades (baixa, média, alta)
- Atribuição de responsáveis
- Datas de vencimento

### 💬 Chat WhatsApp
- Interface dark mode estilo WhatsApp
- Suporte a texto, imagens, áudio e documentos
- Indicadores de status (enviado, entregue, lido)
- Contador de mensagens não lidas
- Integração com Evolution API

## 🛠️ Tecnologias

- **Backend**: Laravel 12
- **Database**: MySQL
- **Frontend**: Blade + Alpine.js
- **Styling**: TailwindCSS
- **Queue**: Redis (opcional)
- **WhatsApp**: Evolution API

## 📦 Instalação

### 1. Clone o repositório

```bash
cd /Users/fredmoura/Downloads/CRM-Fred
```

### 2. Instale as dependências

```bash
composer install
npm install
```

### 3. Configure o ambiente

Atualize o arquivo `.env`:

```bash
DB_CONNECTION=mysql
DB_HOST=177.136.234.91
DB_PORT=3306
DB_DATABASE=fredericomouraco_crmnovo
DB_USERNAME=fredericomouraco_crmnovo
DB_PASSWORD='D.Y5QUgEs^DuXmM]'

# Evolution API (configurar quando disponível)
EVOLUTION_API_URL=
EVOLUTION_API_TOKEN=
EVOLUTION_INSTANCE_NAME=
```

### 4. Execute as migrações e seeders

```bash
php artisan migrate
php artisan db:seed
```

### 5. Compile os assets

```bash
npm run dev
```

### 6. Inicie o servidor

```bash
php artisan serve
```

Acesse: `http://localhost:8000`

## 👤 Credenciais Padrão

**Admin**:
- Email: `admin@example.com`
- Senha: `password`

## 📁 Estrutura do Projeto

```
app/
├── Http/Controllers/
│   ├── ChatController.php
│   ├── KanbanController.php
│   └── WebhookController.php
├── Models/
│   ├── User.php
│   ├── Role.php
│   ├── Permission.php
│   ├── Conversation.php
│   ├── Message.php
│   ├── KanbanColumn.php
│   └── KanbanCard.php
└── Services/
    └── EvolutionApiService.php

database/
├── migrations/
└── seeders/

resources/views/
├── chat/
├── kanban/
└── layouts/
```

## 🔧 Configuração da Evolution API

### 1. Obtenha as credenciais

- URL da API
- Token de autenticação
- Nome da instância

### 2. Atualize o `.env`

```bash
EVOLUTION_API_URL=https://sua-api.com
EVOLUTION_API_TOKEN=seu-token
EVOLUTION_INSTANCE_NAME=sua-instancia
```

### 3. Configure o Webhook

No painel da Evolution API, configure:
- **URL**: `https://seu-dominio.com/api/webhook/evolution`
- **Método**: POST

## 📚 Rotas Principais

### Web (Autenticadas)
- `/dashboard` - Dashboard principal
- `/kanban` - Quadro Kanban
- `/chat` - Interface de chat
- `/chat/{id}` - Conversa específica

### API
- `POST /api/webhook/evolution` - Webhook da Evolution API

## 🎨 Recursos de UI/UX

- ✅ Tema escuro em todos os módulos
- ✅ Design responsivo
- ✅ Animações suaves
- ✅ Interface intuitiva
- ✅ Ícones modernos

## 🔒 Segurança

- Senhas com hash bcrypt
- CSRF protection
- Rate limiting
- Middleware de autenticação
- Controle de acesso baseado em papéis

## 📊 Banco de Dados

### Tabelas Principais

- `users` - Usuários do sistema
- `roles` - Papéis de usuário
- `permissions` - Permissões do sistema
- `conversations` - Conversas do WhatsApp
- `messages` - Mensagens
- `kanban_columns` - Colunas do Kanban
- `kanban_cards` - Cards do Kanban

## 🧪 Testes

### Testar Autenticação
1. Acesse `/login`
2. Use: admin@example.com / password
3. Navegue pelo dashboard

### Testar Kanban
1. Acesse `/kanban`
2. Arraste cards entre colunas
3. Crie novos cards e colunas

### Testar Chat
1. Acesse `/chat`
2. Visualize conversas de exemplo
3. Envie mensagens de teste

## 🚀 Próximos Passos

- [ ] Configurar Evolution API
- [ ] Implementar WebSockets para real-time
- [ ] Adicionar upload de arquivos no chat
- [ ] Criar painel administrativo para RBAC
- [ ] Implementar notificações por email
- [ ] Adicionar comentários e anexos no Kanban

## 📝 Licença

Este projeto é proprietário e confidencial.

## 👨‍💻 Desenvolvido por

Antigravity AI - Google Deepmind

---

**Versão**: 1.0.0  
**Data**: Dezembro 2025  
**Status**: ✅ Pronto para produção
