# Laravel MQTT API Bridge

Ponte entre o MQTT e uma API REST. Um listener em Laravel fica em loop escutando o tópico `semaforo/estado` no broker `broker.hivemq.com`, salva cada mensagem no banco (tabela `logs`) e a API expõe esses dados para outro aplicativo consumir.

```
ESP32 / MQTTX  ──►  broker.hivemq.com  ──►  Listener (artisan mqtt:listen)  ──►  MySQL (logs)  ──►  API GET /api/logs  ──►  outro app
```

## Mensagem esperada

Tópico: `semaforo/estado`

```json
{
  "dispositivo": "semaforo",
  "cor": "verde",
  "porta": 26,
  "estado": true,
  "ciclo": true
}
```

Mensagens que não forem JSON válido, ou que não tiverem todos os campos, são ignoradas pelo listener.

## Requisitos

- Docker e Docker Compose
- (opcional) MQTTX para testar a publicação
- (opcional) DataGrip ou outro cliente SQL para ver o banco

## Passo a passo

Todos os comandos devem ser executados na raiz do projeto. O serviço PHP do `docker-compose.yml` se chama `app`.

### 1. Configurar o `.env`

```bash
cp .env.example .env
```

Dentro do Docker, o host do banco é o nome do serviço do MySQL, não `127.0.0.1`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_mqtt
DB_USERNAME=laravel
DB_PASSWORD=laravel
```

### 2. Subir os containers

```bash
docker compose up -d --build
```

Confira se estão rodando:

```bash
docker compose ps
```

### 3. Instalar as dependências

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

A biblioteca MQTT (`php-mqtt/client`) já faz parte do `composer.json`. Se precisar instalar manualmente:

```bash
docker compose exec app composer require php-mqtt/client
```

### 4. Criar as tabelas

```bash
docker compose exec app php artisan migrate
```

A tabela `logs` é criada com as colunas:

| Coluna        | Tipo      |
|---------------|-----------|
| `id`          | bigint    |
| `dispositivo` | string    |
| `cor`         | string    |
| `porta`       | integer   |
| `estado`      | boolean   |
| `ciclo`       | boolean   |
| `created_at`  | timestamp (data e hora do recebimento) |
| `updated_at`  | timestamp |

### 5. Iniciar o listener MQTT

Deixe um terminal exclusivo para ele:

```bash
docker compose exec app php artisan mqtt:listen
```

Deve aparecer:

```
Conectado. Aguardando mensagens em semaforo/estado...
```

Para parar, use `Ctrl+C`. O listener precisa estar rodando para as mensagens serem salvas. Se ele estiver parado, o que for publicado nesse período não é gravado.

> Rode **apenas uma instância** do listener por vez. O client ID é fixo, então dois listeners ao mesmo tempo se derrubam no broker.

### 6. Testar o fluxo

Publique uma mensagem pelo MQTTX com:

- **Host:** `broker.hivemq.com`
- **Porta:** `1883`
- **Tópico:** `semaforo/estado`
- **Payload (JSON):**

```json
{"dispositivo":"semaforo","cor":"verde","porta":26,"estado":true,"ciclo":true}
```

No terminal do listener deve aparecer algo como `Salvo #1: verde (estado=on)`, e o registro deve estar na tabela `logs`.

### 7. Consultar a API

Troque `8000` pela porta publicada no seu `docker-compose.yml`.

| Método | Rota                     | Descrição                                      |
|--------|--------------------------|------------------------------------------------|
| GET    | `/api/logs`              | Lista até 100 registros, em ordem crescente de `id` |
| GET    | `/api/logs?since=ID`     | Lista só os registros com `id` maior que `ID`  |
| GET    | `/api/logs/ultimo`       | Retorna o último registro recebido             |

Exemplos:

```bash
curl http://localhost:8000/api/logs
curl "http://localhost:8000/api/logs?since=10"
curl http://localhost:8000/api/logs/ultimo
```

O outro aplicativo pode guardar o maior `id` que já recebeu e chamar `/api/logs?since=ID` para buscar apenas os registros novos.

## Estrutura principal

```
app/Console/Commands/MqttListen.php   # listener MQTT (php artisan mqtt:listen)
app/Models/Log.php                    # model da tabela logs
database/migrations/*_create_logs_table.php
routes/api.php                        # rotas da API
```

## Problemas comuns

**`getaddrinfo for mysql failed` ao rodar `php artisan` no Windows**
O `DB_HOST=mysql` só existe dentro da rede do Docker. Rode os comandos pelo container (`docker compose exec app php artisan ...`).

**A tabela `logs` só tem `id`, `created_at` e `updated_at`**
O container não está enxergando os arquivos editados no Windows. Confira se o serviço `app` tem o volume do projeto mapeado (por exemplo `- .:/var/www/html`) e rode `docker compose up -d`. Depois recrie as tabelas com `docker compose exec app php artisan migrate:fresh` (apaga os dados).

**`There are no commands defined in the "mqtt" namespace`**
O `$signature` em `MqttListen.php` precisa ser exatamente `'mqtt:listen'`. Verifique também com `docker compose exec app php artisan optimize:clear`.

**`Automatic reconnects cannot be used together with the clean session flag`**
Use `$mqtt->connect($settings, false);` (sem clean session) quando a reconexão automática estiver ativada.

**Alterei o `MqttListen.php` e nada mudou**
O comando carrega o código uma única vez. Pare com `Ctrl+C` e inicie de novo.

**O listener desconecta e reconecta em loop**
Provavelmente há duas instâncias rodando com o mesmo client ID, ou outra pessoa está usando o mesmo ID no broker público. Mantenha uma só instância ou mude o client ID no `MqttListen.php`.

## Observações

- O broker `broker.hivemq.com` é público: qualquer pessoa pode publicar no tópico `semaforo/estado`. Por isso o listener valida o payload antes de salvar. Para uso real, utilize um broker próprio ou um tópico único e autenticação.
- A API não tem autenticação. Se for exposta fora do ambiente local, proteja-a (por exemplo, com token ou Laravel Sanctum).
- Se o front-end que consome a API estiver em outro endereço, ajuste o CORS em `config/cors.php`.