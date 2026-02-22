import type { FastifyInstance } from 'fastify';
import type { HealthResponse, VersionedHealthResponse } from '../types/contracts.js';
import { createBearerAuth } from '../plugins/auth.js';

interface HealthRouteDependencies {
  coreToken: string;
  getUptimeSeconds(): number;
  checkDatabaseHealth(): Promise<boolean>;
}

export function registerHealthRoutes(app: FastifyInstance, dependencies: HealthRouteDependencies): void {
  app.get('/health', async (): Promise<HealthResponse> => {
    return {
      status: 'ok',
      service: 'core',
      uptime_sec: dependencies.getUptimeSeconds(),
    };
  });

  app.get(
    '/v1/health',
    { preHandler: createBearerAuth(dependencies.coreToken) },
    async (): Promise<VersionedHealthResponse> => {
      const isDatabaseHealthy = await dependencies.checkDatabaseHealth();

      return {
        status: isDatabaseHealthy ? 'ok' : 'degraded',
        version: 'v1',
        db: {
          status: isDatabaseHealthy ? 'ok' : 'error',
        },
      };
    },
  );
}
