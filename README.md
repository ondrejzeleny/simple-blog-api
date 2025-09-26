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
- Zkopírujte .env soubor `cp .env.example .env.local`
- Upravte v souboru .env připojení k DB dle compose.yaml
- Spusťte Docker `docker compose up -d --build`
- Spusťte migraci databáze `docker compose exec server php bin/console doctrine:migrations:migrate`
- Vygenerujte JWT SSH keys `docker compose exec server php bin/console lexik:jwt:generate-keypair`
- Připravte testovací data `docker compose exec server php bin/console doctrine:fixtures:load`

## Poznámky
- Po spuštění běží aplikace na `http://localhost:8002`
- Endpointy jsou zabezpečené, je třeba je volat s autorizační hlavičkou `Bearer JWT_TOKEN`

## Endpointy
- TODO

## Testy
- Připravte testovací databázi `docker compose exec server php bin/console doctrine:database:create --env=test`
- Vygenerujte schéma `docker compose exec server php bin/console --env=test doctrine:schema:create --env=test`
- Připravte testovací data `docker compose exec server php bin/console doctrine:fixtures:load --env=test`
- Ukázkové testy můžete spustit pomocí `docker compose exec --build --rm server php bin/phpunit tests`

## PHP stan
- Pro spuštění PHP stan level 8 zavolejte `php ./vendor/bin/phpstan analyse src tests --level 8`

## Možné zlepšení
- API rate limiter
- Více testů
- API start a limit parametr