export interface HealthResponse {
  status: 'ok';
  service: 'core';
  uptime_sec: number;
}

export interface VersionedHealthResponse {
  status: 'ok' | 'degraded';
  version: 'v1';
  db: {
    status: 'ok' | 'error';
  };
}
