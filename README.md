# Instagram Clone

Aplicação web inspirada no Instagram, desenvolvida como projeto final do curso.

O sistema permite cadastro e autenticação de usuários, edição de perfil, publicação de imagens e vídeos, curtidas, comentários, busca de usuários e sistema de seguidores.

O backend foi desenvolvido como uma API REST utilizando Laravel e organizado no padrão MSC (Model–Service–Controller). O frontend foi desenvolvido com Vue.js e consome a API através do Axios.

## Tecnologias

### Backend

- PHP 8.4
- Laravel
- Laravel Sanctum
- MySQL 8.4
- Composer
- Swagger UI

### Frontend

- Vue.js 3
- Vue Router
- Pinia
- Axios
- Tailwind CSS
- Vite

### Infraestrutura

- Docker
- Docker Compose
- Nginx

## Funcionalidades

- Registro de usuários
- Login e logout
- Autenticação via token com Laravel Sanctum
- Visualização e edição do próprio perfil
- Alteração de nome, username, bio e foto de perfil
- Visualização de outros perfis
- Seguir e deixar de seguir usuários
- Criação de publicações com imagem ou vídeo
- Exclusão das próprias publicações
- Curtir e remover curtida de publicações
- Visualização e criação de comentários
- Feed de publicações
- Sugestões de usuários para seguir
- Pesquisa de usuários por nome ou username
- Página individual de publicação
- Tratamento de rotas inexistentes
- Seeders para geração de dados de teste
- Documentação da API com Swagger UI

## Estrutura do projeto

```text
instagram-clone/
├── compose.yaml
├── .env.example
├── README.md
│
├── backend/
│   ├── Dockerfile
│   ├── Dockerfile.dev
│   ├── .dockerignore
│   ├── .env.example
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── ...
│
└── frontend/
    ├── Dockerfile
    ├── .dockerignore
    ├── .env.example
    ├── src/
    └── ...
```

## Pré-requisitos

Para executar o projeto é necessário ter instalado:

- Git
- Docker
- Docker Compose

Não é necessário instalar PHP, Composer, Node.js ou MySQL diretamente na máquina, pois esses componentes são executados através dos containers Docker.

## Instalação

### 1. Clone o repositório

```bash
git clone <URL_DO_REPOSITORIO>
```

Entre na pasta do projeto:

```bash
cd <NOME_DA_PASTA>
```

### 2. Configure as variáveis de ambiente da raiz

Copie:

```bash
cp .env.example .env
```

No Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

O arquivo contém as configurações utilizadas pelo Docker Compose, por exemplo:

```env
VITE_API_URL=http://localhost:8000/api

MYSQL_DATABASE=instagram
MYSQL_USER=instagram
MYSQL_PASSWORD=instagram
MYSQL_ROOT_PASSWORD=root
```

### 3. Configure o backend

Copie o arquivo de exemplo:

```bash
cp backend/.env.example backend/.env
```

No PowerShell:

```powershell
Copy-Item backend/.env.example backend/.env
```

Confira principalmente as configurações do banco:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=instagram
DB_USERNAME=instagram
DB_PASSWORD=instagram
```

O valor:

```env
DB_HOST=mysql
```

é utilizado porque `mysql` é o nome do serviço do banco de dados dentro da rede Docker.

### 4. Configure o frontend

Copie:

```bash
cp frontend/.env.example frontend/.env
```

No PowerShell:

```powershell
Copy-Item frontend/.env.example frontend/.env
```

A URL da API deve estar configurada como:

```env
VITE_API_URL=http://localhost:8000/api
```

### 5. Construa e inicie os containers

Na raiz do projeto:

```bash
docker compose up -d --build
```

Verifique os containers:

```bash
docker compose ps
```

Os serviços esperados são:

```text
frontend
api
mysql
```

### 6. Gere a chave da aplicação Laravel

```bash
docker compose exec api php artisan key:generate
```

### 7. Execute as migrations e seeders

```bash
docker compose exec api php artisan migrate --seed
```

### 8. Crie o link para os arquivos públicos

```bash
docker compose exec api php artisan storage:link
```

### 9. Acesse a aplicação

Frontend:

```text
http://localhost:5173
```

API:

```text
http://localhost:8000
```

## Banco de dados

O MySQL é executado em um container Docker e os dados são mantidos através de um volume persistente.

Para acessar o banco a partir de uma ferramenta como DBeaver:

```text
Host: localhost
Porta: 3307
Database: instagram
Usuário: instagram
Senha: instagram
```

Dentro da rede Docker, a API utiliza:

```text
Host: mysql
Porta: 3306
```

Portanto:

```text
DBeaver                  Docker
localhost:3307  ──────→  mysql:3306
```

## Documentação da API

A API possui documentação interativa através do Swagger UI.

Após iniciar os containers, a documentação pode ser acessada em:

```text
<URL_DO_SWAGGER>
```

A documentação contém os principais endpoints relacionados a:

- autenticação;
- usuários;
- perfis;
- publicações;
- seguidores;
- curtidas;
- comentários;
- busca;
- feed.

## Comandos úteis

Iniciar os containers:

```bash
docker compose up -d
```

Iniciar e reconstruir as imagens:

```bash
docker compose up -d --build
```

Visualizar os containers:

```bash
docker compose ps
```

Visualizar os logs:

```bash
docker compose logs -f
```

Logs apenas da API:

```bash
docker compose logs -f api
```

Executar comandos Artisan:

```bash
docker compose exec api php artisan <comando>
```

Executar os seeders:

```bash
docker compose exec api php artisan db:seed
```

Limpar os caches do Laravel:

```bash
docker compose exec api php artisan optimize:clear
```

Parar os containers:

```bash
docker compose down
```

Parar os containers e remover também os volumes:

```bash
docker compose down -v
```

> Atenção: `docker compose down -v` remove o volume do MySQL e, consequentemente, os dados armazenados no banco.

## Docker

O projeto utiliza um único arquivo:

```text
compose.yaml
```

na raiz para orquestrar os três serviços:

```text
frontend
   ↓
Vue + Nginx

api
   ↓
Laravel

mysql
   ↓
MySQL 8.4
```

O backend possui dois Dockerfiles:

```text
Dockerfile.dev
```

utilizado para desenvolvimento, e:

```text
Dockerfile
```

preparado para produção.

O frontend possui um único `Dockerfile`, utilizando multi-stage build com Node.js para gerar a aplicação e Nginx para servir os arquivos finais.

## Variáveis de ambiente

Arquivos `.env` reais não são versionados.

O repositório contém arquivos `.env.example` com as configurações necessárias para executar o projeto.

Nunca adicione senhas ou outros segredos reais aos arquivos `.env.example`.

## Arquitetura do backend

O backend segue o padrão MSC:

```text
Request
   ↓
Controller
   ↓
Service
   ↓
Model
   ↓
Database
```

### Model

Responsável pelas entidades e relacionamentos com o banco de dados.

### Service

Responsável pelas regras de negócio da aplicação.

### Controller

Responsável por receber as requisições HTTP, chamar os Services e retornar as respostas da API.

## Autenticação

A API utiliza Laravel Sanctum.

Após o login, a API retorna um token de autenticação que é enviado pelo frontend nas requisições protegidas:

```http
Authorization: Bearer <token>
```

O Axios é configurado para adicionar o token automaticamente às requisições autenticadas.

## Principais relacionamentos

```text
User 1:N Post

User 1:N Comment

Post 1:N Comment

User N:N Post
     via likes

User N:N User
     via follows
```

## Persistência

O banco MySQL utiliza um volume Docker para preservar os dados mesmo após os containers serem interrompidos.

Executar:

```bash
docker compose down
```

não remove os dados.

Já:

```bash
docker compose down -v
```

remove os volumes e apaga o banco de desenvolvimento.

## Licença

Projeto desenvolvido para fins acadêmicos e educacionais.