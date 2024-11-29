# Projeto API de Comentários

Este projeto consiste em uma API robusta desenvolvida em Laravel para gerenciar comentários de usuários, implementando autenticação, autorização, verificação de e-mail, redefinição de senha e outras funcionalidades avançadas para garantir segurança e integridade dos dados.

Foi realizado para completar o desafio em questão: https://github.com/Betalabs/Selecao-Back-End/blob/master/README.md

## Características
Uma API completa com funcionalidades de autenticação, CRUD de comentários e medidas de segurança robustas, incluindo middleware de administrador e expiração de tokens.

## Funcionalidades

### Funcionalidades Obrigatórias
1. **Registro de Usuários**: Cadastro com nome, e-mail e senha.
   - **Implementação**: `AuthController::register`
2. **Autenticação de Usuários**: Login para obtenção de tokens.
   - **Implementação**: `AuthController::login`

3. **CRUD de Comentários**:
   - **Criação**: Usuários autenticados podem criar comentários. `CommentController::store`
   - **Leitura**: Qualquer usuário pode visualizar comentários. `CommentController::index`
   - **Atualização**: Apenas comentários próprios podem ser editados. `CommentController::update`
   - **Exclusão**: Apenas comentários próprios podem ser excluídos. `CommentController::destroy`
4. **Histórico de Atualizações**: Histórico detalhado por comentário.
   - **Implementação**: `CommentController::history`

### Funcionalidades Desejáveis
- **Autenticação com Tokens**: Usando Laravel Sanctum.
- **Validação e Tratamento de Erros**: Mensagens claras e entradas validadas.
- **Boas Práticas de Código**: Conformidade com as convenções Laravel.
- **Testes Automatizados**: Implementados com PHPUnit.

### Funcionalidades Adicionais
1. **Expiração de Tokens**:
   - Middleware `TokenExpiration`: Expira tokens após 24 horas.
   - Comando `TokenCleanup`: Limpeza de tokens expirados.
2. **Middleware de Administrador**:
   - Apenas administradores podem acessar rotas específicas.
3. **Verificação de E-mail**:
   - Integração com Mailtrap para envio de notificações.
4. **Reset de Senha**:
   - Fluxo completo para recuperação de senha com token.

---
## Instalação
- Conta no **Mailtrap**
- Gerar a Chave da Aplicação: php artisan key:generate
- Executar Migrações e Seeders: php artisan migrate --seed
- Configurar Mailtrap: Adicione as credenciais no arquivo .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_FROM_ADDRESS="no-reply@seuapp.com"
MAIL_FROM_NAME="${APP_NAME}"

- Inicie o servidor de desenvolvimento: php artisan serve

## Endpoints da API

| Endpoint                           | Método  | Descrição                     |
|------------------------------------|---------|--------------------------------|
| `/api/register`                    | POST    | Registro de Usuário           |
| `/api/login`                       | POST    | Login de Usuário              |
| `/api/logout`                      | POST    | Logout de Usuário             |
| `/api/user`                        | PUT     | Atualizar Perfil              |
| `/api/comments`                    | GET     | Listar Comentários            |
| `/api/user/comments`               | POST    | Criar Comentário              |
| `/api/user/comments/{id}`          | PUT     | Atualizar Comentário          |
| `/api/user/comments/{id}`          | DELETE  | Excluir Comentário            |
| `/api/comments/{id}/history`       | GET     | Histórico de Comentário       |
| `/api/admin/user`                  | GET     | Listar Usuários (Admin)       |
| `/api/admin/user/{id}`             | DELETE  | Excluir Usuário (Admin)       |
| `/api/forgot-password`             | POST    | Solicitar Reset de Senha      |
| `/api/reset-password`              | POST    | Resetar Senha                 |
| `/api/email/verify/{id}/{hash}`    | GET     | Verificar E-mail              |

---

## Segurança

### Expiração de Tokens
- **TokenExpiration Middleware**: Tokens expiram após 24 horas.
- **TokenCleanup Command**: Remove tokens expirados diariamente.

### Middleware de Administrador
- **AdminMiddleware**: Controla o acesso a rotas específicas, garantindo que apenas administradores possam acessá-las.

### Verificação de E-mail
- **Mailtrap**: Envia notificações para verificar e-mails após registro.

### Reset de Senha
- **Token de Expiração**: Link para redefinição enviado via e-mail.

---

## Seeders

### AdminUserSeeder
- Cria um usuário administrador padrão com `is_admin = true`.
- **Execução**:
  ```bash
  php artisan db:seed --class=AdminUserSeeder

## Testes Automatizados

- **Framework**: PHPUnit
- **Cobertura**: 
  - Registro
  - Login
  - CRUD de comentários
  - Verificação de e-mail
  - Reset de senha
- **Execução**:
  ```bash
  php artisan test


## Notas

- **Validações**: Todas as entradas são verificadas para garantir a integridade dos dados.
- **Tratamento de Erros**: Mensagens claras são fornecidas ao usuário em caso de falhas nas requisições.
- **Organização do Código**: O projeto segue os padrões de projeto e as melhores práticas do framework Laravel.
# betalabs_challenge
