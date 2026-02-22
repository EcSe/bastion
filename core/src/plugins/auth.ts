import type { FastifyReply, FastifyRequest, preHandlerHookHandler } from 'fastify';
import { timingSafeEqual } from 'node:crypto';

function tokensMatch(providedToken: string, expectedToken: string): boolean {
  const providedBuffer = Buffer.from(providedToken);
  const expectedBuffer = Buffer.from(expectedToken);

  if (providedBuffer.length !== expectedBuffer.length) {
    return false;
  }

  return timingSafeEqual(providedBuffer, expectedBuffer);
}

export function createBearerAuth(expectedToken: string): preHandlerHookHandler {
  return async function bearerAuth(request: FastifyRequest, reply: FastifyReply): Promise<void> {
    const authorization = request.headers.authorization;

    if (!authorization || !authorization.startsWith('Bearer ')) {
      await reply.code(401).send({ message: 'Unauthorized' });
      return;
    }

    const token = authorization.slice(7).trim();

    if (!tokensMatch(token, expectedToken)) {
      await reply.code(401).send({ message: 'Unauthorized' });
      return;
    }
  };
}
