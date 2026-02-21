# Bastion

Base del proyecto Bastion con arquitectura bimodal:
- `console/`: aplicación Laravel (UI/Auth/Auditoría)
- `core/`: motor Node.js + TypeScript (LLM/recipes/ejecución)
- `recipes/`: recetas YAML
- `scripts/`: scripts operativos/despliegue
- `docs/requirements/`: documentos base de producto/técnicos

## Fuentes oficiales de requerimientos
Estos documentos mandan sobre el alcance y la dirección del proyecto:

1. `docs/requirements/BASTION_PRD_v0.2.md`
2. `docs/requirements/BASTION_TECH_SPEC_v0.2.md`

## Estado actual
- Fase A iniciada y base técnica implementada.
- Monorepo activo (`console/` + `core/`).
- Core base operativo con endpoints:
1. `GET /health`
2. `GET /v1/health` (Bearer + check DB)
- Integración inicial de Console hacia Core por `CoreClient`.

## Roadmap de trabajo

### Fase A — Base de plataforma (en progreso)
1. Estructura monorepo `console/`, `core/`, `recipes/`, `scripts/` ✅
2. Core Fastify + TypeScript + configuración de entorno ✅
3. Health endpoints + autenticación Bearer interna ✅
4. Conectividad MariaDB en Core (health DB) ✅
5. Ajuste CI para monorepo (console/core) ✅

### Fase B — Inventario operativo (pendiente)
1. Modelo de datos v1 en Console: `targets`, `recipes`, `executions`, `execution_logs`, `approvals`
2. CRUD de Targets (alta/edición/baja + tags + activación)
3. CRUD de Recipes (riesgo, timeout, parámetros tipados, versión)
4. Loader de recipes en Core + `GET /v1/recipes`

### Fase C — Orquestación controlada (pendiente)
1. `POST /v1/intent` con mapeo de intención a recipe allowlist
2. Validación estricta de parámetros (tipo, enum, rango, regex)
3. Motor de approvals (risk >= 2) con confirm/reject
4. `POST /v1/execute` + creación de ejecución y estados

### Fase D — Ejecución y observabilidad (pendiente)
1. Ejecutor SSH por steps con stdout/stderr incremental
2. Persistencia de logs y resultado final por ejecución
3. Historial en Console con filtros y detalle de ejecución
4. Polling de estado/logs y manejo de timeout/errores/expiración

## Criterio para no perder el rumbo
Antes de implementar cualquier funcionalidad nueva, validar siempre:
1. ¿Está respaldada por PRD/Tech Spec?
2. ¿Respeta allowlist de recipes y política de riesgo?
3. ¿Deja trazabilidad auditable en DB?
4. ¿Incluye prueba automatizada mínima?

## Comandos base de trabajo

### Console
1. `cd /Users/ecse/Herd/bastion/console`
2. `hc install`
3. `art test --compact`

### Core
1. `cd /Users/ecse/Herd/bastion/core`
2. `npm install`
3. `npm run dev`
4. `npm test`
