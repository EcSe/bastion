import { describe, expect, it } from 'vitest';
import { buildApp } from '../src/app.js';

describe('health routes', () => {
  it('returns base health without authentication', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({ method: 'GET', url: '/health' });
    const payload = response.json();

    expect(response.statusCode).toBe(200);
    expect(payload.status).toBe('ok');
    expect(payload.service).toBe('core');

    await app.close();
  });

  it('rejects v1 health without a bearer token', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({ method: 'GET', url: '/v1/health' });

    expect(response.statusCode).toBe(401);

    await app.close();
  });

  it('returns degraded when database health fails', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => false,
    });

    const response = await app.inject({
      method: 'GET',
      url: '/v1/health',
      headers: {
        authorization: 'Bearer token',
      },
    });

    expect(response.statusCode).toBe(200);
    expect(response.json()).toEqual({
      status: 'degraded',
      version: 'v1',
      db: {
        status: 'error',
      },
    });

    await app.close();
  });
});
