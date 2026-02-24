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

  it('rejects v1 health with an invalid bearer token', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({
      method: 'GET',
      url: '/v1/health',
      headers: {
        authorization: 'Bearer invalid-token',
      },
    });

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

  it('rejects execute without a bearer token', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({
      method: 'POST',
      url: '/v1/execute',
      payload: {
        recipe_id: 1,
        target_id: 2,
        params: {
          branch: 'main',
        },
      },
    });

    expect(response.statusCode).toBe(401);

    await app.close();
  });

  it('queues execution when payload and token are valid', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({
      method: 'POST',
      url: '/v1/execute',
      headers: {
        authorization: 'Bearer token',
      },
      payload: {
        recipe_id: 1,
        target_id: 2,
        params: {
          branch: 'main',
        },
      },
    });

    expect(response.statusCode).toBe(202);
    expect(response.json()).toEqual({
      status: 'queued',
    });

    await app.close();
  });

  it('accepts execute payload with params as array', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({
      method: 'POST',
      url: '/v1/execute',
      headers: {
        authorization: 'Bearer token',
      },
      payload: {
        recipe_id: 1,
        target_id: 2,
        params: [],
      },
    });

    expect(response.statusCode).toBe(202);

    await app.close();
  });

  it('returns validation error for malformed execute payload', async () => {
    const app = buildApp({
      coreToken: 'token',
      checkDatabaseHealth: async () => true,
    });

    const response = await app.inject({
      method: 'POST',
      url: '/v1/execute',
      headers: {
        authorization: 'Bearer token',
      },
      payload: {
        recipe_id: 'invalid-id',
        target_id: 2,
      },
    });

    expect(response.statusCode).toBe(422);

    await app.close();
  });
});
