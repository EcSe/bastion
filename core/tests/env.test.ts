import { describe, expect, it } from 'vitest';
import { readEnv } from '../src/config/env.js';

describe('readEnv', () => {
  it('throws when a required variable is missing', () => {
    expect(() => {
      readEnv({
        DB_HOST: '127.0.0.1',
        DB_NAME: 'bastion',
        DB_USER: 'root',
      });
    }).toThrow('CORE_TOKEN');
  });

  it('returns defaults and casts numeric variables', () => {
    const env = readEnv({
      CORE_TOKEN: 'token-value',
      DB_HOST: '127.0.0.1',
      DB_NAME: 'bastion',
      DB_USER: 'root',
      DB_PASS: 'secret',
    });

    expect(env.host).toBe('127.0.0.1');
    expect(env.port).toBe(8787);
    expect(env.database.port).toBe(3306);
  });
});
