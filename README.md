# CloneGram

Aplicação web inspirada no Instagram, desenvolvida como projeto final do curso.

O projeto possui uma **API REST em Laravel** e um **frontend em Vue.js**, permitindo cadastro e autenticação de usuários, edição de perfil, publicações com imagem ou vídeo, curtidas, comentários, seguidores, busca de usuários e feed.

Todo o ambiente pode ser executado com Docker por meio de um único arquivo `compose.yaml` localizado na raiz do projeto.

---

## Funcionalidades

- Cadastro de usuários
- Login e logout
- Autenticação com Laravel Sanctum
- Persistência da autenticação por token
- Visualização e edição do próprio perfil
- Alteração de nome, username, bio e foto de perfil
- Visualização de outros perfis
- Seguir e deixar de seguir usuários
- Contagem de seguidores e usuários seguidos
- Criação de publicações
- Upload de imagens e vídeos
- Exclusão das próprias publicações
- Curtir e remover curtidas
- Visualização e criação de comentários
- Feed de publicações
- Sugestões de usuários para seguir
- Busca de usuários por nome ou username
- Visualização individual de uma publicação
- Página 404 para rotas inexistentes
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
- Apache na imagem de produção da API

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

Responsável pela representação das entidades e pelos relacionamentos com o banco de dados.

### Service

Responsável pelas regras de negócio da aplicação.

### Controller

Responsável por receber as requisições HTTP, chamar os serviços necessários e retornar as respostas da API.

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

Não é necessário instalar localmente PHP, Composer, MySQL, Node.js ou Nginx, pois esses componentes são executados pelos containers.

---

# Instalação

## 1. Clone o repositório

```bash
git clone <URL_DO_REPOSITORIO>
```

Entre na pasta do projeto:

```bash
cd ProjetoFinal
```

---

## 2. Configure o ambiente da raiz

Crie o arquivo `.env` a partir do exemplo.

### Linux / macOS

```bash
cp .env.example .env
```

### Windows PowerShell

```powershell
Copy-Item .env.example .env
```

O arquivo `.env.example` da raiz pode conter:

```env
VITE_API_URL=http://localhost:8000/api

MYSQL_DATABASE=instagram
MYSQL_USER=instagram
MYSQL_PASSWORD=change_me
MYSQL_ROOT_PASSWORD=change_me
```

Depois de copiar o arquivo, substitua `change_me` por senhas locais de sua escolha.

Exemplo:

```env
VITE_API_URL=http://localhost:8000/api

MYSQL_DATABASE=instagram
MYSQL_USER=instagram
MYSQL_PASSWORD=minha_senha_local
MYSQL_ROOT_PASSWORD=minha_senha_root_local
```

> Os arquivos `.env` reais não devem ser enviados ao repositório.

---

## 3. Configure a API Laravel

Crie o arquivo da API:

### Linux / macOS

```bash
cp api/.env.example api/.env
```

### Windows PowerShell

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
DB_PASSWORD=minha_senha_local
```

A senha definida em:

```env
DB_PASSWORD
```

deve ser a mesma utilizada em:

```env
MYSQL_PASSWORD
```

no `.env` da raiz.

## Atenção ao `APP_URL`

Utilize:

```env
APP_URL=http://localhost:8000
```

O Laravel utiliza essa configuração para gerar URLs absolutas.

Se ela estiver configurada incorretamente, imagens, vídeos e fotos de perfil podem ser gerados com URLs inválidas.

---

## 4. Configure o frontend

Crie:

### Linux / macOS

```bash
cp frontend/.env.example frontend/.env
```

### Windows PowerShell

```powershell
Copy-Item frontend/.env.example frontend/.env
```

Configure:

```env
VITE_API_URL=http://localhost:8000/api
```

Essa variável define o endereço utilizado pelo frontend para acessar a API.

Ela não é uma informação secreta, pois é utilizada pelo JavaScript executado no navegador.

---

# Iniciando a aplicação

Na raiz do projeto, execute:

```bash
docker compose up -d --build
```

Confira o estado dos containers:

```bash
docker compose ps
```

Os serviços esperados são:

```text
frontend
api
mysql
```

O MySQL deve aparecer como saudável.

---

# Configuração inicial do Laravel

Depois que os containers estiverem ativos, execute:

```bash
docker compose exec api php artisan key:generate
```

Limpe os caches:

```bash
docker compose exec api php artisan optimize:clear
```

Execute migrations e seeders:

```bash
docker compose exec api php artisan migrate --seed
```

Crie o link público do storage:

```bash
docker compose exec api php artisan storage:link
```

O `storage:link` é necessário para disponibilizar publicamente arquivos como fotos de perfil, imagens e vídeos dos posts.

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

## Swagger UI

```text
http://localhost:8000/docs
```

---

# Banco de dados

O banco MySQL é executado pelo Docker.

Dentro da rede Docker, a API utiliza:

```text
Host: mysql
Porta: 3306
```

Por isso, no Laravel:

```env
DB_HOST=mysql
DB_PORT=3306
```

O nome `mysql` funciona porque ele é o nome do serviço dentro da rede criada pelo Docker Compose.

---

# Acessando o banco pelo DBeaver

A porta do MySQL é publicada somente no host local:

```yaml
ports:
  - "127.0.0.1:3307:3306"
```

No DBeaver, utilize:

```text
Host: localhost
Porta: 3307
Database: valor de MYSQL_DATABASE
Username: valor de MYSQL_USER
Password: valor de MYSQL_PASSWORD
```

Fluxo:

```text
DBeaver
localhost:3307
       ↓
Docker
       ↓
mysql:3306
```

Não utilize `mysql` como host no DBeaver. Esse nome existe somente dentro da rede Docker.

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

> Atenção: `docker compose down -v` remove os dados do banco armazenados no volume.

---

# Desenvolvimento do backend

O serviço da API utiliza um bind mount:

```yaml
volumes:
  - ./api:/var/www/html
  - api_vendor:/var/www/html/vendor
```

Isso significa que a pasta local:

```text
./api
```

é montada dentro do container em:

```text
/var/www/html
```

Alterações feitas no código Laravel são refletidas imediatamente no container.

Por exemplo:

```text
api/app/Services/PostService.php
```

é visto no container como:

```text
/var/www/html/app/Services/PostService.php
```

Mudanças comuns em Controllers, Services, Models, Resources, Requests, Policies e Routes normalmente não exigem rebuild da imagem.

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

Para desenvolvimento diretamente com Vite, também é possível executar o frontend fora do container:

```bash
cd frontend
npm install
npm run dev
```

Nesse caso, mantenha:

```env
VITE_API_URL=http://localhost:8000/api
```

configurado em `frontend/.env`.

---

# Autenticação

A API utiliza Laravel Sanctum.

Após um login válido, a API retorna um token.

O frontend envia esse token nas requisições protegidas através do header:

```http
Authorization: Bearer <token>
```

O Axios adiciona o token automaticamente às requisições autenticadas.

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

Os arquivos públicos ficam em:

```text
storage/app/public
```

e são disponibilizados através de:

```text
public/storage
```

Esse link é criado com:

```bash
docker compose exec api php artisan storage:link
```

Caso imagens, vídeos ou fotos de perfil não carreguem, confira:

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

A API possui documentação interativa com Swagger UI.

Acesse:

```text
http://localhost:8000/docs
```

A documentação contém os endpoints relacionados a autenticação, usuários, perfis, posts, seguidores, curtidas, comentários, busca e feed.

---

# Principais endpoints

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

Para detalhes de parâmetros e respostas, consulte:

```text
http://localhost:8000/docs
```

---

# Comandos úteis

Subir os containers:

```bash
docker compose up -d
```

Construir e subir:

```bash
docker compose up -d --build
```

Ver containers:

```bash
docker compose ps
```

Ver logs:

```bash
docker compose logs -f
```

Logs da API:

```bash
docker compose logs -f api
```

Logs do frontend:

```bash
docker compose logs -f frontend
```

Logs do MySQL:

```bash
docker compose logs -f mysql
```

Entrar no container da API:

```bash
docker compose exec api bash
```

Executar comandos Artisan:

```bash
docker compose exec api php artisan <comando>
```

Limpar caches:

```bash
docker compose exec api php artisan optimize:clear
```

Executar migrations:

```bash
docker compose exec api php artisan migrate
```

Executar seeders:

```bash
docker compose exec api php artisan db:seed
```

Parar containers:

```bash
docker compose down
```

Parar containers e remover volumes:

```bash
docker compose down -v
```

---

# Variáveis de ambiente e segurança

Os arquivos `.env` reais não devem ser versionados.

Arquivos que podem ser enviados ao Git:

```text
.env.example
api/.env.example
frontend/.env.example
compose.yaml
```

Arquivos que não devem ser enviados:

```text
.env
api/.env
frontend/.env
```

Os arquivos `.env.example` podem conter nomes de banco, usuário e placeholders, mas não devem conter senhas reais, tokens, chaves ou outros segredos.

Exemplo:

```env
MYSQL_PASSWORD=change_me
MYSQL_ROOT_PASSWORD=change_me
```

Nunca coloque no repositório:

```env
APP_KEY=chave_real
DB_PASSWORD=senha_real
MAIL_PASSWORD=senha_real
AWS_SECRET_ACCESS_KEY=segredo_real
GITHUB_TOKEN=token_real
```

---

# Testando uma instalação limpa

Uma forma de verificar se o projeto pode ser executado em outra máquina é remover completamente containers e volumes:

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
docker compose exec api php artisan optimize:clear
docker compose exec api php artisan migrate --seed
docker compose exec api php artisan storage:link
```

Acesse:

```text
http://localhost:5173
```

> Atenção: esse procedimento remove o banco de desenvolvimento existente.

---

# Git

Confirme que o `.gitignore` inclui pelo menos:

```gitignore
.env
api/.env
frontend/.env

api/vendor/
frontend/node_modules/
frontend/dist/
```

Não ignore:

```text
.env.example
api/.env.example
frontend/.env.example
compose.yaml
api/Dockerfile
api/Dockerfile.dev
frontend/Dockerfile
composer.lock
package-lock.json
```

Antes do push, você pode verificar se algum `.env` real está sendo versionado:

```bash
git ls-files | grep -E '(^|/)\.env$'
```

Se nenhum resultado aparecer, os `.env` reais não estão sendo rastreados pelo Git.

---

# Objetivo acadêmico

O projeto foi desenvolvido para aplicar conceitos de:

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

Projeto desenvolvido para fins educacionais.
