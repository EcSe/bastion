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

export function readEnv(source: EnvSource = process.env): CoreEnv {
  for (const key of requiredKeys) {
    if (!source[key] || source[key]?.trim() === '') {
      throw new Error(`Missing required environment variable: ${key}`);
    }
  }

  return {
    host: source.HOST?.trim() || '127.0.0.1',
    port: Number(source.PORT || '8787'),
    coreToken: source.CORE_TOKEN!.trim(),
    database: {
      host: source.DB_HOST!.trim(),
      port: Number(source.DB_PORT || '3306'),
      name: source.DB_NAME!.trim(),
      user: source.DB_USER!.trim(),
      pass: source.DB_PASS || '',
    },
  };
}
