import Fastify, { type FastifyInstance } from 'fastify';
import { registerHealthRoutes } from './routes/health.js';

export interface AppDependencies {
  coreToken: string;
  checkDatabaseHealth(): Promise<boolean>;
}

export function buildApp(dependencies: AppDependencies): FastifyInstance {
  const app = Fastify({ logger: true });
  const startedAt = process.hrtime.bigint();

  registerHealthRoutes(app, {
    coreToken: dependencies.coreToken,
    checkDatabaseHealth: dependencies.checkDatabaseHealth,
    getUptimeSeconds(): number {
      const elapsedNanoseconds = process.hrtime.bigint() - startedAt;

      return Number(elapsedNanoseconds / 1000000000n);
    },
  });

  return app;
}
