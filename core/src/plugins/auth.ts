import type { FastifyReply, FastifyRequest, preHandlerHookHandler } from 'fastify';

export function createBearerAuth(expectedToken: string): preHandlerHookHandler {
  return async function bearerAuth(request: FastifyRequest, reply: FastifyReply): Promise<void> {
    const authorization = request.headers.authorization;

    if (!authorization || !authorization.startsWith('Bearer ')) {
      await reply.code(401).send({ message: 'Unauthorized' });
      return;
    }

    const token = authorization.slice(7).trim();

    if (token !== expectedToken) {
      await reply.code(401).send({ message: 'Unauthorized' });
    }
  };
}
