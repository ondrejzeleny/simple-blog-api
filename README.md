# Simple Blog API

REST API pro správu blogu postavené na Symfony frameworku s Doctrine ORM. Aplikace poskytuje CRUD operace pro články a uživatele s jednoduchým role-based přístupem.

## Funkce
- **Autentizace a autorizace** - JWT token based autentizace s role-based přístupem
- **Správa uživatelů** - CRUD operace pro uživatele (pouze admin)
- **Správa článků** - CRUD operace pro články s vlastnickými právy
- **Role systém** - Admin, Author, Reader s různými oprávněními
- **Validace dat** - Symfony Validator pro vstupní data
- **Testy** - PHPUnit testy s testovací databází

## Požadavky
- PHP 8.2
- Composer
- Docker (včetně docker compose)

## Spuštění

### 1. Příprava prostředí
```bash
# Stáhněte si projekt a otevřete složku projektu
git clone <repository-url>
cd simple-blog-api

# Nainstalujte composer závislosti
composer install

# Zkopírujte konfigurační soubory
cp .env.example .env.local
cp .env.test.example .env.test.local
```

### 2. Spuštění Docker kontejnerů
```bash
# Spusťte Docker kontejnery
docker compose up -d --build
```

### 3. Inicializace databáze
```bash
# Vytvořte databázi
docker compose exec server php bin/console doctrine:database:create --if-not-exists

# Spusťte migrace
docker compose exec server php bin/console doctrine:migrations:migrate

# Vygenerujte JWT SSH klíče
docker compose exec server php bin/console lexik:jwt:generate-keypair

# Načtěte testovací data
docker compose exec server php bin/console doctrine:fixtures:load
```

### 4. Ověření spuštění
Aplikace běží na `http://localhost:8002`

## Autentizace

Všechny endpointy (kromě registrace a přihlášení) vyžadují JWT token v hlavičce:
```
Authorization: Bearer {JWT_TOKEN}
```

JWT token získáte přihlášením na endpointu `/auth/login`.

## API Endpointy

### Registrace a přihlášení

#### POST /auth/register
Registrace nového uživatele.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "name": "John Doe",
    "role": "reader"
}
```

**Response:**
```json
{
    "message": "User registered successfully."
}
```

#### POST /auth/login
Přihlášení uživatele, vrací JWT token.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

### Správa uživatelů (pouze pro admin)

#### GET /users
Seznam všech uživatelů.

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

**Response:**
```json
[
    {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "role": "admin"
    }
]
```

#### GET /users/{id}
Získání dat o konkrétním uživateli.

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

#### POST /users
Vytvoření nového uživatele.

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

**Request Body:**
```json
{
    "email": "newuser@example.com",
    "password": "password123",
    "name": "New User",
    "role": "author"
}
```

#### PUT /users/{id}
Úprava uživatele.

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

**Request Body:**
```json
{
    "email": "updated@example.com",
    "name": "Updated Name",
    "role": "author"
}
```

#### DELETE /users/{id}
Smazání uživatele.

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

### Správa článků

#### GET /articles
Seznam všech článků.

**Role:** Reader, Author, Admin

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

**Response:**
```json
[
    {
        "id": 1,
        "title": "Article Title",
        "content": "Article content...",
        "author_id": 1,
        "created_at": "2024-01-01 12:00:00",
        "updated_at": "2024-01-01 12:00:00"
    }
]
```

#### GET /articles/{id}
Získání článku podle ID.

**Role:** Reader, Author, Admin

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

#### POST /articles
Vytvoření nového článku.

**Role:** Author, Admin

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

**Request Body:**
```json
{
    "title": "New Article",
    "content": "Article content..."
}
```

#### PUT /articles/{id}
Úprava článku.

**Role:** Author (vlastník článku), Admin

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

**Request Body:**
```json
{
    "title": "Updated Title",
    "content": "Updated content..."
}
```

#### DELETE /articles/{id}
Smazání článku.

**Role:** Author (vlastník článku), Admin

**Headers:**
```
Authorization: Bearer {JWT_TOKEN}
```

## Role a oprávnění

- **Admin** - plný přístup ke všem operacím
- **Author** - může vytvářet a upravovat vlastní články
- **Reader** - může pouze číst články

## Testovací uživatelé

Po načtení fixtures jsou k dispozici tyto testovací účty:

- **Admin**: `admin@example.com` / `password1`
- **Author**: `author@example.com` / `password2`  
- **Reader**: `reader@example.com` / `password3`

## Testy

- **Unit testy** - testují jednotlivé třídy v izolaci s mockováním, lze spustit samostatně
- **Integrační testy** - testují celé workflow s reálnou databází a fixtures
- Před spuštěním testů je potřeba provést krok **1. Příprava prostředí**

### Unit testy
```bash
# Spuštění
docker compose run --build --rm server php bin/phpunit tests/Unit
```

### Integrační testy
```bash
# Příprava
docker compose up -d --build
docker compose exec server php bin/console doctrine:database:create --env=test
docker compose exec server php bin/console doctrine:schema:create --env=test
docker compose exec server php bin/console doctrine:fixtures:load --env=test
docker compose exec server php bin/console lexik:jwt:generate-keypair

# Spuštění
docker compose exec server php bin/phpunit tests/Integration
```

## Kvalita kódu

### PHPStan
```bash
# Spuštění PHPStan level 10
docker compose exec server php ./vendor/bin/phpstan analyse src --level 10

# Testy splňují PHPStan level 8
docker compose exec server php ./vendor/bin/phpstan analyse tests --level 8
```

Aplikace odpovídá PHPStan level 10.
Testy odpovídají PHPStan level 8.

## Technologie

- **Framework**: Symfony 7.x
- **ORM**: Doctrine ORM
- **Autentizace**: JWT (LexikJWTAuthenticationBundle)
- **Validace**: Symfony Validator
- **Testování**: PHPUnit
- **Kvalita kódu**: PHPStan
- **Kontejnerizace**: Docker + Docker Compose

## Možná vylepšení
- **Enum role** - oddělit role do Enumu a s tím související refactoring
- **Error responses** - samostatná služba zajišťující jednoté error responses
- **Errog handling** - lepší a jednoznačnější error hlášky
- **Práce s účty** - schvalování účtů, logout endpoint atd.
- **API Rate Limiting** - omezení počtu požadavků
- **Pagination** - stránkování pro seznamy, pro větší množství dat
- **Produkce** - příprava na spuštění v produkci
