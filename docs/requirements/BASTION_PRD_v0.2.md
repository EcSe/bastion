# BASTION — PRD (Product Requirements Document) v0.2

Fecha: 2026-02-21  
Autor: Elvin Salinas  
Estado: Base para implementación (Codex-ready)

## 1. Resumen ejecutivo

Bastion es un orquestador personal de infraestructura potenciado por IA para ejecutar operaciones recurrentes (diagnóstico, mantenimiento, despliegues, logs, backups, etc.) desde móvil o escritorio, de forma segura, confirmable y auditable, sobre una red privada (Tailscale).

Principio rector: la IA interpreta intención, pero la ejecución solo ocurre mediante “recipes” predefinidas (allowlist).

## 2. Problema

Cuando el usuario no está frente a su workstation, operar servidores y equipos (personal, trabajo, Mac y futuro Windows) se vuelve lento y fragmentado (apps SSH, scripts sueltos, notas, RDP, etc.). Falta orquestación, control de riesgo, trazabilidad y una UX móvil consistente.

## 3. Objetivos (v1)

• Ejecutar recetas seguras vía SSH en targets registrados.
• Interfaz PWA (móvil) para chat/acción, confirmaciones y visualización de resultados.
• Política de riesgo: acciones sensibles requieren confirmación explícita.
• Auditoría completa: quién ejecutó qué, contra qué target, cuándo, parámetros y salida.
• Operación 24/7 en servidor personal, sin exponer puertos públicos (solo Tailscale).

## 4. No-objetivos (v1)

• Autonomía sin control (no “ejecutar comandos libres”).
• Multi-tenant y roles enterprise.
• Marketplace de herramientas/skills.
• Cobertura “cualquier cosa” tipo RPA; el foco es infraestructura.

## 5. Usuarios y escenarios

Usuario primario (v1): Elvin (admin único).
Escenarios prioritarios:
1) Ver logs: “muéstrame los últimos 200 lines de Nginx del server X”.
2) Estado: CPU/RAM/Disk, procesos críticos, uptime, servicios.
3) Deploy controlado: pull + migrate + restart (con confirmación).
4) Mantenimiento: rotación/limpieza de logs, reinicio de servicios, backups.
5) (Fase 2) Integraciones: email/calendario (lectura), resúmenes y recordatorios.

## 6. Requisitos funcionales

RF1. Gestión de Targets: alta/edición/baja; credenciales/llaves; tags; validación de conectividad.
RF2. Gestión de Recipes: CRUD; versión; parámetros tipados; nivel de riesgo; timeout; pasos.
RF3. Chat → Intención: el usuario envía mensaje; el sistema propone recipe+params (no comandos raw).
RF4. Confirmaciones: para riesgo ≥2 se crea “pending approval”; hasta aprobar no ejecuta.
RF5. Ejecución: correr recipe, registrar output incremental y resultado final.
RF6. Historial: listado filtrable por target, recipe, fecha, estado; detalle con logs.
RF7. Observabilidad básica: estado del core/worker, cola, errores recientes.
RF8. Seguridad operativa: revocación de tokens/sesiones, rotación de llaves, bloqueo por intentos.

## 7. Requisitos no funcionales

RNF1. Seguridad: zero-trust con Tailscale; no exponer endpoints al público.
RNF2. Confiabilidad: reintentos controlados; timeouts; idempotencia donde aplique.
RNF3. Auditabilidad: logs inmutables y exportables.
RNF4. Rendimiento: respuestas UI < 500ms para acciones de consulta (sin contar ejecución).
RNF5. Mantenibilidad: recipes declarativas; separación clara entre UI, policy y executor.

## 8. Principios de seguridad (no negociables)

1) La IA nunca ejecuta shell libre. Solo selecciona recipes existentes.
2) Allowlist estricta: recipe registry define exactamente qué puede correr.
3) Validación de parámetros por tipo/regex/rango; sanitización.
4) Confirmación obligatoria según riesgo (y opcional 2FA para riesgo 3).
5) Secretos fuera del repo (env/vault); llaves con permisos mínimos.
6) Todo request autenticado, firmado y con rate-limit.

## 9. Métricas de éxito

• 10 recipes reales ejecutadas desde móvil sin acceso público.
• 0 ejecuciones de comandos no permitidos (bloqueo correcto).
• Tiempo de entrega v1: panel + 3 recipes críticas funcionando.
• Tasa de fallos < 2% por problemas no determinísticos (red/SSH).

## 10. Roadmap por hitos

H0 (Setup): repos + entorno local + Tailscale OK.
H1 (MVP): targets + recipes + ejecutar 1 recipe segura + historial.
H2 (Seguridad): risk engine + confirmaciones + auditoría completa.
H3 (Robustez): colas/streaming de logs + reintentos + monitoreo.
H4 (Producto): hardening + packaging + instalador + docs para terceros.

## 11. Alcance v1 (entregables concretos para Codex)

• App Console (Laravel): Auth, PWA shell, CRUD targets/recipes, chat, approvals, historial.
• Motor de ejecución (ver Tech Spec): ejecutar recipes por SSH, registrar logs.
• Base de datos: tablas de targets, recipes, executions, execution_logs, approvals.
• 3 recipes iniciales: (a) status server, (b) tail logs, (c) deploy básico.
• Documentación: cómo agregar recipes y cómo desplegar en servidor personal.

