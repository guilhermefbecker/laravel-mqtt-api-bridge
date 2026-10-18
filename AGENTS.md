# AGENTS.md - Laravel MQTT API Bridge

## Execution (CRITICAL)
- **Docker-only**: NEVER run `php`, `artisan`, or `composer` directly on Windows. Always use `docker compose exec app <command>`.
- **App service**: `app` is the PHP service name in `docker-compose.yml` (serves on port 8080:8000). 
- **Migrations**: ONLY use `docker compose exec app php artisan migrate`. DO NOT run `migrate:fresh`, `migrate:reset`, or `db:wipe` - these destroy the real `logs` table data.
- **Listener**: Run MQTT listener with `docker compose exec app php artisan mqtt:listen`. Run only ONE instance (fixed client ID). Stop with Ctrl+C and restart after code changes.

## Protected Files/Routes (DO NOT CHANGE WITHOUT APPROVAL)
- `app/Console/Commands/MqttListen.php` - MQTT listener (signature `mqtt:listen`)
- `app/Models/Log.php` - Log model
- `app/Http/Controllers/LogController.php` - Logs controller
- `app/Http/Resources/LogResource.php` - Log resource
- `routes/api.php` - API routes (`/api/logs`, `/api/logs/novos`, `/api/logs/ultimo`) 
- `database/migrations/*create_logs_table.php` - logs table structure
- `public/semaforo/` - original JS frontend (reference only; do NOT delete until validated)

## Commands
- **Setup**: `docker compose up -d --build` then `docker compose exec app composer install` then `docker compose exec app php artisan key:generate`
- **Migrate**: `docker compose exec app php artisan migrate`
- **MQTT listen**: `docker compose exec app php artisan mqtt:listen`
- **Clear caches**: `docker compose exec app php artisan optimize:clear` (useful if commands/routes not updating)
- **Tests**: `docker compose exec app php artisan test` (PHPUnit; check `phpunit.xml` for config)

## Architecture Notes
- **Flow**: MQTT (broker.hivemq.com, topic `semaforo/estado`) -> `MqttListen` command -> MySQL `logs` table -> API (`LogController` + routes) -> consumer.
- **Validation**: Listener ignores non-JSON and validates required fields (`dispositivo`, `cor`, `porta`, `estado`, `ciclo`) before saving.
- **API behavior**: `GET /api/logs` returns up to 100 ordered by `id`; `GET /api/logs?since=ID` returns IDs > ID; `GET /api/logs/ultimo` returns latest by id or fails.
- **Naming**: Portuguese for tables/columns/routes/UI text (match original).
- **DB host**: Inside Docker, MySQL host is `mysql` (not `127.0.0.1`); see `.env.example`.

## Workflow Requirements
- **Plan first**: Before any large change, show the plan and wait for approval.
- **Report**: After each step, list files created/changed and explain how to test.
- **Verify**: Run relevant checks after changes (e.g. `docker compose exec app php artisan test` if tests affected).

# Contexto do projeto

- Projeto Laravel rodando em Docker. NUNCA rodar php, artisan ou composer direto no Windows;
  usar sempre `docker compose exec app <comando>`.
- NUNCA rodar migrate:fresh, migrate:reset ou db:wipe (apagam a tabela logs, que tem dados reais).
  Só `php artisan migrate`.
- Já existem e NÃO devem ser alterados sem eu pedir: app/Console/Commands/MqttListen.php (listener MQTT),
  app/Models/Log.php, a tabela logs, app/Http/Controllers/LogController.php,
  e routes/api.php (/api/logs, /api/logs/ultimo).
- O front-end original em JavaScript puro está copiado em public/semaforo/ (application/, assets/).
  Usar como referência. Não apagar nada dessa pasta até eu validar.
- Nomes de tabelas, colunas, rotas e textos da interface em português, como no projeto original.
- Antes de qualquer mudança grande, mostre o plano e espere minha aprovação.
- Ao terminar cada etapa, liste os arquivos criados e alterados e explique como testar.