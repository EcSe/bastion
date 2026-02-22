import mysql, { type Pool } from 'mysql2/promise';

export interface DatabaseHealthChecker {
  check(): Promise<boolean>;
  close(): Promise<void>;
}

interface DatabaseConnectionConfig {
  host: string;
  port: number;
  database: string;
  user: string;
  password: string;
}

class MysqlHealthChecker implements DatabaseHealthChecker {
  public constructor(private readonly pool: Pool) {}

  public async check(): Promise<boolean> {
    try {
      const connection = await this.pool.getConnection();
      await connection.ping();
      connection.release();

      return true;
    } catch {
      return false;
    }
  }

  public async close(): Promise<void> {
    await this.pool.end();
  }
}

export function createDatabaseHealthChecker(config: DatabaseConnectionConfig): DatabaseHealthChecker {
  const pool = mysql.createPool({
    host: config.host,
    port: config.port,
    database: config.database,
    user: config.user,
    password: config.password,
    waitForConnections: true,
    connectionLimit: 5,
  });

  return new MysqlHealthChecker(pool);
}
