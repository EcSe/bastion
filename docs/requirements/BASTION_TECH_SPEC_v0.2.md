# BASTION — Especificación Técnica (Tech Spec) v0.2

Fecha: 2026-02-21  
Estado: Base para implementación (Codex-ready)  
Contexto: Servidor personal con MariaDB+PHP+Nginx y npm, sin Docker

## 1. Decisión arquitectónica

Arquitectura objetivo (v1): Bimodal.

• Bastion Console (Laravel) — Producto/UI/Auth/Auditoría.
• Bastion Core (Node.js + TypeScript) — Motor always-on (LLM + recipes + SSH + colas/streaming).

Nota operativa: si se prioriza máxima velocidad y mínimo cambio cognitivo, es posible implementar v1 “Laravel-only”. Sin embargo, este documento especifica la ruta bimodal por ser la base más escalable y natural para ejecución persistente.

## 2. Estructura de repositorio (monorepo recomendado)

bastion/
  console/     (Laravel)
  core/        (Node + TS)
  recipes/     (YAML)
  docs/        (PRD + Tech Spec)
  scripts/     (deploy, systemd, nginx)

Herd solo sirve la carpeta console/. El core corre como proceso aparte (dev: npm run dev; prod: systemd o PM2).

## 3. Stack tecnológico

Console:
- Laravel 11/12, PHP 8.2+ (8.3 recomendado)
- Livewire 3 + Alpine
- Auth: Sanctum
- DB: MariaDB

Core:
- Node.js 20 LTS + TypeScript
- HTTP: Fastify
- SSH: ssh2
- Queue: BullMQ + Redis (recomendado)
- Process manager: systemd (prod) / PM2 (dev opcional)

Red privada:
- Tailscale en server + clientes (iPhone/Mac/targets)

## 4. Flujo end-to-end

1) Usuario (PWA) envía mensaje.
2) Console llama a Core: POST /v1/intent {message, context}.
3) Core llama al LLM con function calling y retorna {recipe_id, params, risk_level, summary}.
4) Console presenta “plan” y, si risk>=2, solicita confirmación.
5) Console confirma: POST /v1/approvals/{id}/confirm.
6) Core encola ejecución (BullMQ) y produce logs incrementales.
7) Console consume estado/logs (polling o WebSocket) y muestra resultado.
8) Todo queda persistido en DB (audit trail).

## 5. API Core ↔ Console

Base URL interna: http://127.0.0.1:8787 (dev) o 127.0.0.1 en producción.
Versionado: /v1

Endpoints:
- POST /v1/intent
- POST /v1/execute
- POST /v1/approvals/{id}/confirm
- POST /v1/approvals/{id}/reject
- GET  /v1/recipes
- GET  /v1/targets
- GET  /v1/executions
- GET  /v1/executions/{id}
- GET  /v1/executions/{id}/logs (paginado)
- WS   /v1/ws/executions/{id} (opcional v1.1)

Auth:
- Token compartido (HMAC o Bearer) solo entre Console↔Core + Tailscale.
- Rate-limit + IP allowlist (100% Tailscale range).

## 6. Modelo de datos (MariaDB)

Tablas mínimas:

targets
- id, name, host, port, user, auth_method, key_path/secret_ref, tags(json), is_active, created_at

recipes
- id, slug, name, description, risk_level (0-3), timeout_sec
- parameters(json schema simple)
- steps(yaml/text)
- version, is_active, created_at

executions
- id, recipe_id, target_id, status (queued/running/success/failed/cancelled)
- requested_by (user_id), params(json), started_at, finished_at, exit_code, error_summary

execution_logs
- id, execution_id, ts, stream (stdout/stderr/system), line(text)

approvals
- id, execution_id (nullable hasta crear), requested_by, status (pending/approved/rejected/expired)
- risk_level, summary, expires_at, approved_at, approved_by

Observación: approvals puede existir antes de execution; al aprobar, se crea/activa la ejecución.

## 7. Recipes (YAML) — formato mínimo y reglas

Formato recomendado:

id: deploy_app
name: Deploy App X
risk_level: 2
timeout_sec: 900
parameters:
  branch:
    type: string
    enum: [main, develop]
steps:
  - name: Pull
    run: "cd /var/www/app && git fetch --all && git checkout {{branch}} && git pull"
  - name: Migrate
    run: "cd /var/www/app && php artisan migrate --force"
  - name: Restart
    run: "sudo systemctl restart php8.3-fpm"

Reglas no negociables:
- No permitir parámetros no declarados.
- No permitir interpolación fuera de {{param}}.
- Validar enum/rango/regex antes de ejecutar.
- Timeout por step y por recipe.
- En risk>=2: requiere approval.

## 8. LLM: function calling / tool registry

El Core genera un “tool schema” a partir de recipes activas:
- tool: run_recipe
- args: {recipe_id, params}

El LLM solo puede elegir recipe_id dentro del registry y proponer params válidos. Si sugiere algo fuera, se rechaza y se responde con alternativas (recipes existentes).

## 9. Ejecutor SSH

Implementación Core:
- Cada step se ejecuta en sesión SSH capturando stdout/stderr incremental.
- Sanitizar/escapar parámetros (ideal: parametrizar en recipe con validación estricta).
- Usuario dedicado por target con permisos mínimos.
- Sudo solo para comandos exactos vía /etc/sudoers allowlist.

## 10. Desarrollo local (Mac + Herd)

Local:
- Herd sirve console/ (ej. https://bastion.test).
- Core se levanta en terminal:
  cd core
  npm i
  npm run dev

Variables:
Console .env:
  CORE_URL=http://127.0.0.1:8787
  CORE_TOKEN=...

Core .env:
  PORT=8787
  CORE_TOKEN=...
  DB_HOST=127.0.0.1
  DB_NAME=bastion
  DB_USER=...
  DB_PASS=...

## 11. Producción (sin Docker)

Servicios:
- Nginx para Console
- Core como systemd service (recomendado)
- Redis (recomendado) como servicio del sistema

Hardening:
- firewall bloquea puerto del core a internet; accesible solo local/tailscale.
- tokens rotables; expiración approvals; rate-limit.
- logs append-only; backups DB.

## 12. Plan de implementación (para Codex)

Fase A:
- Monorepo, console Laravel, core Fastify TS, /health, conexión DB.
Fase B:
- CRUD targets/recipes en console.
- Loader de recipes en core y /v1/recipes.
Fase C:
- /v1/intent (LLM) + validación + approvals + /execute.
- Ejecutor SSH + logs.
Fase D:
- UI de historial + detalle + logs (polling).
- Timeouts, errores, expiración approvals.

