import { readEnv } from './config/env.js';
import { createDatabaseHealthChecker } from './plugins/db.js';
import { buildApp } from './app.js';

const env = readEnv();

const healthChecker = createDatabaseHealthChecker({
  host: env.database.host,
  port: env.database.port,
  database: env.database.name,
  user: env.database.user,
  password: env.database.pass,
});

const app = buildApp({
  coreToken: env.coreToken,
  checkDatabaseHealth(): Promise<boolean> {
    return healthChecker.check();
  },
});

const gracefulShutdown = async (): Promise<void> => {
  await app.close();
  await healthChecker.close();
  process.exit(0);
};

process.on('SIGINT', () => {
  void gracefulShutdown();
});

process.on('SIGTERM', () => {
  void gracefulShutdown();
});

app
  .listen({ host: env.host, port: env.port })
  .catch(async (error: unknown) => {
    app.log.error(error);
    await healthChecker.close();
    process.exit(1);
  });
