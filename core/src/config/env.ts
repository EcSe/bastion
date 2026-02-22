export interface CoreEnv {
  host: string;
  port: number;
  coreToken: string;
  database: {
    host: string;
    port: number;
    name: string;
    user: string;
    pass: string;
  };
}

interface EnvSource {
  [key: string]: string | undefined;
}

const requiredKeys = ['CORE_TOKEN', 'DB_HOST', 'DB_NAME', 'DB_USER'] as const;

function parsePort(value: string | undefined, defaultValue: number, key: string): number {
  const parsedValue = Number.parseInt(value ?? String(defaultValue), 10);

  if (Number.isNaN(parsedValue) || parsedValue < 1 || parsedValue > 65535) {
    throw new Error(`Invalid environment variable ${key}: expected an integer between 1 and 65535`);
  }

  return parsedValue;
}

export function readEnv(source: EnvSource = process.env): CoreEnv {
  for (const key of requiredKeys) {
    if (!source[key] || source[key]?.trim() === '') {
      throw new Error(`Missing required environment variable: ${key}`);
    }
  }

  return {
    host: source.HOST?.trim() || '127.0.0.1',
    port: parsePort(source.PORT, 8787, 'PORT'),
    coreToken: source.CORE_TOKEN!.trim(),
    database: {
      host: source.DB_HOST!.trim(),
      port: parsePort(source.DB_PORT, 3306, 'DB_PORT'),
      name: source.DB_NAME!.trim(),
      user: source.DB_USER!.trim(),
      pass: source.DB_PASS || '',
    },
  };
}
