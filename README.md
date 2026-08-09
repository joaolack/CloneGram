# Instagram Clone

Aplicação web inspirada no Instagram, desenvolvida como projeto final do curso.

O projeto possui uma **API REST em Laravel** e um **frontend em Vue.js**, permitindo interação entre usuários por meio de publicações, curtidas, comentários e seguidores.

Todo o ambiente pode ser executado com Docker através de um único arquivo `compose.yaml` localizado na raiz do projeto.

---

## Funcionalidades

A aplicação possui:

- Cadastro de usuários
- Login e logout
- Autenticação com Laravel Sanctum
- Persistência da sessão através de token
- Visualização do próprio perfil
- Edição de:
  - nome
  - username
  - bio
  - foto de perfil
- Visualização de perfis de outros usuários
- Seguir e deixar de seguir usuários
- Contagem de seguidores e seguindo
- Criação de publicações
- Upload de imagens
- Upload de vídeos
- Exclusão das próprias publicações
- Curtir e remover curtidas
- Visualização de comentários
- Criação de comentários
- Feed de publicações
- Sugestões de usuários para seguir
- Busca por nome ou username
- Visualização individual de uma publicação
- Tratamento de rotas inexistentes com página 404
- Seeders para popular o banco de desenvolvimento
- Documentação da API com Swagger UI

---

# Tecnologias

## Backend

- PHP 8.4
- Laravel
- Laravel Sanctum
- MySQL 8.4
- Composer
- Swagger UI

## Frontend

- Vue.js 3
- Vue Router
- Pinia
- Axios
- Tailwind CSS
- Vite

## Infraestrutura

- Docker
- Docker Compose
- Nginx
- Apache para imagem de produção da API

---

# Arquitetura

O backend segue o padrão **MSC — Model, Service, Controller**.

```text
Request HTTP
     ↓
Controller
     ↓
Service
     ↓
Model
     ↓
MySQL
```

### Model

Responsável pela representação das entidades e seus relacionamentos com o banco de dados.

### Service

Responsável pelas regras de negócio da aplicação.

### Controller

Responsável por receber requisições HTTP, chamar os serviços necessários e devolver as respostas da API.

---

# Estrutura do projeto

```text
ProjetoFinal/
│
├── compose.yaml
├── .env
├── .env.example
├── .gitignore
├── README.md
│
├── api/
│   ├── Dockerfile
│   ├── Dockerfile.dev
│   ├── .dockerignore
│   ├── .env
│   ├── .env.example
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── storage/
│   ├── composer.json
│   ├── composer.lock
│   └── artisan
│
└── frontend/
    ├── Dockerfile
    ├── .dockerignore
    ├── .env
    ├── .env.example
    ├── src/
    ├── public/
    ├── package.json
    ├── package-lock.json
    └── vite.config.js
```

---

# Docker

O projeto utiliza **um único `compose.yaml` na raiz** para orquestrar os serviços:

```text
Docker Compose
│
├── frontend
│   └── Vue + Nginx
│
├── api
│   └── Laravel
│
└── mysql
    └── MySQL 8.4
```

O backend possui dois Dockerfiles:

```text
api/Dockerfile.dev
```

utilizado no ambiente de desenvolvimento, e:

```text
api/Dockerfile
```

preparado para produção.

O frontend utiliza:

```text
frontend/Dockerfile
```

com multi-stage build:

```text
Node.js
   ↓
npm run build
   ↓
dist/
   ↓
Nginx
```

---

# Pré-requisitos

Para executar a aplicação utilizando Docker é necessário ter instalado:

- Git
- Docker
- Docker Compose

Não é necessário instalar localmente:

- PHP
- Composer
- MySQL
- Node.js
- Nginx

Esses componentes são executados através dos containers.

---

# Instalação

## 1. Clone o repositório

```bash
git clone https://github.com/joaolack/CloneGram.git
```

Entre na pasta:

```bash
cd ProjetoFinal
```

---

## 2. Configure o ambiente do Docker Compose

Crie o `.env` da raiz a partir do exemplo:

### Linux / macOS

```bash
cp .env.example .env
```

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

Exemplo:

```env
VITE_API_URL=http://localhost:8000/api

MYSQL_DATABASE=instagram
MYSQL_USER=instagram
MYSQL_PASSWORD=instagram
MYSQL_ROOT_PASSWORD=root
```

Esse `.env` é utilizado principalmente pelo Docker Compose.

---

## 3. Configure a API Laravel

Crie:

```bash
cp api/.env.example api/.env
```

No PowerShell:

```powershell
Copy-Item api/.env.example api/.env
```

Confira principalmente estas configurações:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=instagram
DB_USERNAME=instagram
DB_PASSWORD=instagram
```

### Atenção ao `APP_URL`

É importante utilizar:

```env
APP_URL=http://localhost:8000
```

e não apenas:

```env
APP_URL=http://localhost
```

O Laravel utiliza essa configuração para gerar URLs absolutas.

Uma configuração incorreta pode fazer com que imagens de posts, vídeos e fotos de perfil sejam geradas com URLs inválidas.

---

## 4. Configure o frontend

Crie:

```bash
cp frontend/.env.example frontend/.env
```

No PowerShell:

```powershell
Copy-Item frontend/.env.example frontend/.env
```

Configure:

```env
VITE_API_URL=http://localhost:8000/api
```

O frontend utiliza essa variável através do Vite:

```js
import.meta.env.VITE_API_URL
```

A URL da API, portanto, não fica fixa diretamente no código-fonte.

---

# Iniciando a aplicação

Na raiz do projeto execute:

```bash
docker compose up -d --build
```

Esse comando irá construir e iniciar:

```text
frontend
api
mysql
```

Confira o estado dos containers:

```bash
docker compose ps
```

O MySQL deve aparecer como saudável:

```text
mysql    Up (healthy)
```

---

# Configurando o Laravel pela primeira vez

Depois que os containers estiverem ativos, gere a chave da aplicação:

```bash
docker compose exec api php artisan key:generate
```

Limpe os caches:

```bash
docker compose exec api php artisan optimize:clear
```

Execute as migrations e os seeders:

```bash
docker compose exec api php artisan migrate --seed
```

Crie o link público do storage:

```bash
docker compose exec api php artisan storage:link
```

O `storage:link` é necessário para disponibilizar publicamente arquivos como:

```text
fotos de perfil
imagens dos posts
vídeos dos posts
```

---

# Acessando a aplicação

## Frontend

```text
http://localhost:5173
```

## API

```text
http://localhost:8000
```

## API REST

```text
http://localhost:8000/api
```

---

# Banco de dados

O banco MySQL é executado pelo Docker.

Dentro da rede Docker, a API utiliza:

```text
Host: mysql
Porta: 3306
```

Por isso o Laravel possui:

```env
DB_HOST=mysql
DB_PORT=3306
```

---

# Persistência do banco

O MySQL utiliza um volume Docker:

```text
db_data
```

Por isso:

```bash
docker compose down
```

remove os containers, mas mantém os dados.

Ao executar novamente:

```bash
docker compose up -d
```

o banco continua com os dados anteriores.

Já:

```bash
docker compose down -v
```

remove também os volumes.

> Atenção: esse comando apaga o banco de dados armazenado pelo Docker.

---

# Desenvolvimento do backend

O serviço da API utiliza um bind mount:

```yaml
volumes:
  - ./api:/var/www/html
  - api_vendor:/var/www/html/vendor
```

Isso significa que o código da pasta:

```text
./api
```

é montado dentro do container em:

```text
/var/www/html
```

Portanto, mudanças em arquivos Laravel são refletidas imediatamente no container.

Por exemplo:

```text
api/app/Services/PostService.php
```

é visto pelo container como:

```text
/var/www/html/app/Services/PostService.php
```

Assim, alterações comuns em:

```text
Controllers
Services
Models
Resources
Requests
Policies
Routes
```

normalmente não exigem reconstruir a imagem Docker.

---

# Dependências do Composer

As dependências PHP ficam em um volume Docker separado:

```text
api_vendor
```

Para instalar um novo pacote:

```bash
docker compose exec api composer require nome/pacote
```

Para reinstalar dependências:

```bash
docker compose exec api composer install
```

---

# Migrations

Depois de criar uma migration:

```bash
docker compose exec api php artisan migrate
```

Para reconstruir completamente o banco de desenvolvimento:

```bash
docker compose exec api php artisan migrate:fresh --seed
```

> Esse comando apaga todos os dados existentes antes de recriar as tabelas.

---

# Desenvolvimento do frontend

O frontend executado pelo Docker é construído com:

```bash
npm run build
```

e servido pelo Nginx.

Por isso, alterações no código Vue não aparecem automaticamente no container atual.

Depois de alterar o frontend, reconstrua o serviço:

```bash
docker compose up -d --build frontend
```

Para desenvolvimento diretamente com o Vite, também é possível executar o frontend fora do container utilizando:

```bash
npm install
npm run dev
```

dentro da pasta:

```text
frontend/
```

Nesse caso, certifique-se de que:

```env
VITE_API_URL=http://localhost:8000/api
```

esteja configurado no `frontend/.env`.

---

# Autenticação

A API utiliza **Laravel Sanctum**.

Após um login válido, a API retorna um token.

O frontend envia esse token nas requisições protegidas através do header:

```http
Authorization: Bearer <token>
```

O Axios adiciona o token automaticamente às requisições autenticadas.

Exceto login e registro, as funcionalidades da aplicação exigem autenticação.

---

# Principais relacionamentos

```text
User 1:N Post

User 1:N Comment

Post 1:N Comment

User N:N Post
     via likes

User N:N User
     via follows
```

As relações de like e follow possuem restrições para impedir duplicidades.

---

# Uploads

As mídias enviadas pela aplicação são armazenadas utilizando o filesystem do Laravel.

Os arquivos públicos ficam associados a:

```text
storage/app/public
```

e são disponibilizados através de:

```text
public/storage
```

criado pelo comando:

```bash
docker compose exec api php artisan storage:link
```

Caso as imagens não carreguem, confira:

```env
APP_URL=http://localhost:8000
```

e execute:

```bash
docker compose exec api php artisan optimize:clear
docker compose exec api php artisan storage:link
```

---

# Documentação da API

A API possui documentação interativa utilizando Swagger UI.

Após iniciar a aplicação, acesse:

```text
http://localhost:8000/docs
```

A documentação apresenta os endpoints relacionados a:

```text
Autenticação
Perfis
Usuários
Posts
Likes
Comentários
Seguidores
Busca
Home
```

---

# Principais endpoints

Alguns dos endpoints disponíveis são:

```text
POST   /api/register
POST   /api/login
POST   /api/logout

GET    /api/me

GET    /api/home

GET    /api/profile
PUT    /api/profile

GET    /api/users
GET    /api/users/{username}

POST   /api/users/{username}/follow
DELETE /api/users/{username}/follow

GET    /api/posts
POST   /api/posts
GET    /api/posts/{post}
DELETE /api/posts/{post}

POST   /api/posts/{post}/like
DELETE /api/posts/{post}/like

GET    /api/posts/{post}/comments
POST   /api/posts/{post}/comments
```

Para consultar detalhes dos parâmetros e respostas, utilize o Swagger UI.

---

# Comandos úteis

## Subir os containers

```bash
docker compose up -d
```

## Construir e subir

```bash
docker compose up -d --build
```

## Ver containers

```bash
docker compose ps
```

## Ver logs

```bash
docker compose logs
```

## Logs em tempo real

```bash
docker compose logs -f
```

## Logs da API

```bash
docker compose logs -f api
```

## Logs do frontend

```bash
docker compose logs -f frontend
```

## Logs do MySQL

```bash
docker compose logs -f mysql
```

## Entrar no container da API

```bash
docker compose exec api bash
```

## Executar Artisan

```bash
docker compose exec api php artisan <comando>
```

## Limpar cache do Laravel

```bash
docker compose exec api php artisan optimize:clear
```

## Executar migrations

```bash
docker compose exec api php artisan migrate
```

## Executar seeders

```bash
docker compose exec api php artisan db:seed
```

## Parar os containers

```bash
docker compose down
```

## Parar e remover volumes

```bash
docker compose down -v
```

---

# Variáveis de ambiente

Os arquivos `.env` reais não devem ser versionados.

O repositório disponibiliza arquivos `.env.example` contendo as variáveis necessárias para executar a aplicação sem expor segredos.

Arquivos esperados:

```text
.env.example
api/.env.example
frontend/.env.example
```

Não coloque senhas reais, tokens ou outros segredos nesses arquivos.

---

# Testando uma instalação limpa

Uma forma de verificar se o projeto pode ser executado em outra máquina é remover completamente os containers e volumes:

```bash
docker compose down -v
```

Depois:

```bash
docker compose up -d --build
```

Execute novamente:

```bash
docker compose exec api php artisan key:generate
docker compose exec api php artisan migrate --seed
docker compose exec api php artisan storage:link
```

E acesse:

```text
http://localhost:5173
```

O projeto também foi testado a partir de um clone em outra máquina utilizando Docker.

---

# Git

Arquivos como estes não devem ser enviados ao repositório:

```text
.env
api/.env
frontend/.env

api/vendor/
frontend/node_modules/
frontend/dist/
```

Arquivos importantes que devem ser versionados:

```text
compose.yaml

.env.example

api/Dockerfile
api/Dockerfile.dev
api/.dockerignore
api/.env.example

frontend/Dockerfile
frontend/.dockerignore
frontend/.env.example

composer.lock
package-lock.json
```

---

# Objetivo acadêmico

O projeto foi desenvolvido com o objetivo de aplicar conceitos de:

- APIs REST
- autenticação e autorização
- Laravel
- arquitetura MSC
- Eloquent ORM
- relacionamentos entre entidades
- upload de arquivos
- Vue.js
- integração frontend/backend
- Docker
- Docker Compose
- bancos de dados relacionais
- documentação de APIs

---

# Licença

Projeto desenvolvido para fins acadêmicos e educacionais.