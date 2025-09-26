# Simple API blog
- Webová aplikace poskutující ukázku API pro objednávky
- Aplikace je postavená na Symfony komponentech a Doctrine
- Konfigurace projektu se nastavuje v souboru .env
- Aplikaci lze spustit v Docker containeru, který obsahuje 3 služby: php-apache, mysql a phpmyadmin

## Požadavky
- PHP 8.3+
- Composer
- Docker (včetně docker compose)

## Spuštění
- Stáněte si projekt a otevřete složku projektu
- Nainstalujte composer závislosti `composer install`
- Zkopírujte .env soubor `cp .env.example .env`
- Upravte v souboru .env připojení k DB dle compose.yaml
- Spusťte Docker `docker compose up -d --build`
- Spusťte migraci databáze `docker compose exec server php ./vendor/bin/doctrine-migrations migrate`
- Připravte testovací data `docker compose exec server php ./bin/fixtures`

## Poznámky
- Po spuštění běží aplikace na `http://localhost:8000`
- Endpointy jsou zabezpečené, je třeba je volat s autorizační hlavičkou `Bearer test`
- Klíč lze změnit v .env

## Endpointy
- TODO

## Testy
- Ukázkové testy můžete spustit pomocí `docker compose run --build --rm server ./vendor/bin/phpunit`

## PHP stan
- Pro spuštění PHP stan level 8 zavolejte `php ./vendor/bin/phpstan analyse src tests --level 8`

## Možné zlepšení
- API rate limiter
- Více testů