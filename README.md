# Simple API blog
- Webová aplikace poskutující ukázku API pro blog
- Aplikace je postavená na Symfony a Doctrine
- Aplikaci lze spustit v Docker containeru, který obsahuje 3 služby: php-apache, mysql a phpmyadmin

## Požadavky
- PHP 8.2
- Composer
- Docker (včetně docker compose)

## Spuštění
- Stáněte si projekt a otevřete složku projektu
- Nainstalujte composer závislosti `composer install`
- Zkopírujte soubor `cp .env.example .env.local`
- Zkopírujte soubor `cp .env.test.example .env.test.local`
- Zkontrolujte a případně upravte v souboru .env.local a .env.test.local připojení k DB dle compose.yaml
- Spusťte Docker `docker compose up -d --build`
- Vytvořte novou databázi `docker compose exec server php bin/console doctrine:database:create --if-not-exists`
- Spusťte migraci databáze `docker compose exec server php bin/console doctrine:migrations:migrate`
- Vygenerujte JWT SSH keys `docker compose exec server php bin/console lexik:jwt:generate-keypair`
- Připravte testovací data `docker compose exec server php bin/console doctrine:fixtures:load`

## Poznámky
- Po spuštění běží aplikace na `http://localhost:8002`
- Endpointy jsou zabezpečené, je třeba je volat s autorizační hlavičkou `Bearer JWT_TOKEN`
- JWT token získáte přihlášením na endpointu /auth/login

## Endpointy
- TODO

## Testy
- Projekt musí být spuštěný
- Připravte testovací databázi `docker compose exec server php bin/console doctrine:database:create --env=test`
- Vygenerujte schéma `docker compose exec server php bin/console doctrine:schema:create --env=test`
- Připravte testovací data `docker compose exec server php bin/console doctrine:fixtures:load --env=test`
- Ukázkové testy můžete spustit pomocí `docker compose exec server php bin/phpunit tests`

## PHP stan
- Pro spuštění PHP stan level 10 zavolejte `php ./vendor/bin/phpstan analyse src --level 10`
- Aplikace odpovídá PHP stan level 10

## Možné zlepšení
- API rate limiter
- Více testů
- API start a limit parametr nebo pagination