import type { FastifyInstance } from 'fastify';
import { createBearerAuth } from '../plugins/auth.js';

interface ExecutionRouteDependencies {
  coreToken: string;
}

interface ExecuteRequestBody {
  recipe_id: number;
  target_id: number;
  params?: Record<string, unknown>;
}

function isValidExecuteBody(body: unknown): body is ExecuteRequestBody {
  if (!body || typeof body !== 'object' || Array.isArray(body)) {
    return false;
  }

  const candidate = body as {
    recipe_id?: unknown;
    target_id?: unknown;
    params?: unknown;
  };

  const hasValidRecipe = Number.isInteger(candidate.recipe_id) && Number(candidate.recipe_id) > 0;
  const hasValidTarget = Number.isInteger(candidate.target_id) && Number(candidate.target_id) > 0;

  if (!hasValidRecipe || !hasValidTarget) {
    return false;
  }

  if (candidate.params === undefined || candidate.params === null) {
    return true;
  }

  return typeof candidate.params === 'object' && !Array.isArray(candidate.params);
}

export function registerExecutionRoutes(app: FastifyInstance, dependencies: ExecutionRouteDependencies): void {
  app.post(
    '/v1/execute',
    { preHandler: createBearerAuth(dependencies.coreToken) },
    async (request, reply): Promise<void> => {
      if (!isValidExecuteBody(request.body)) {
        await reply.code(422).send({
          message: 'Invalid execute payload.',
        });

        return;
      }

      await reply.code(202).send({
        status: 'queued',
      });
    },
  );
}
