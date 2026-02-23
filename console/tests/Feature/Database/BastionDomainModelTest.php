<?php

use App\Models\Approval;
use App\Models\Execution;
use App\Models\ExecutionLog;
use App\Models\Recipe;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

it('creates the bastion domain tables with expected columns', function (): void {
    expect(Schema::hasTable('targets'))->toBeTrue()
        ->and(Schema::hasTable('recipes'))->toBeTrue()
        ->and(Schema::hasTable('executions'))->toBeTrue()
        ->and(Schema::hasTable('execution_logs'))->toBeTrue()
        ->and(Schema::hasTable('approvals'))->toBeTrue();

    expect(Schema::hasColumns('targets', ['name', 'host', 'port', 'user', 'auth_method', 'key_path', 'secret_ref', 'tags', 'is_active']))->toBeTrue();
    expect(Schema::hasColumns('recipes', ['slug', 'name', 'risk_level', 'timeout_sec', 'parameters', 'steps', 'version', 'is_active']))->toBeTrue();
    expect(Schema::hasColumns('executions', ['recipe_id', 'target_id', 'status', 'requested_by', 'params', 'started_at', 'finished_at', 'exit_code']))->toBeTrue();
    expect(Schema::hasColumns('execution_logs', ['execution_id', 'ts', 'stream', 'line']))->toBeTrue();
    expect(Schema::hasColumns('approvals', ['execution_id', 'requested_by', 'status', 'risk_level', 'summary', 'expires_at', 'approved_at', 'approved_by']))->toBeTrue();
});

it('persists and resolves relationships across bastion domain models', function (): void {
    $operator = User::factory()->create();
    $approver = User::factory()->create();

    $target = Target::factory()->create([
        'tags' => ['env' => 'prod', 'role' => 'app'],
    ]);

    $recipe = Recipe::factory()->create([
        'risk_level' => 2,
        'parameters' => [
            'branch' => [
                'type' => 'string',
                'enum' => ['main', 'develop'],
            ],
        ],
    ]);

    $execution = Execution::factory()->running()->create([
        'target_id' => $target->id,
        'recipe_id' => $recipe->id,
        'requested_by' => $operator->id,
        'params' => ['branch' => 'main'],
    ]);

    $log = ExecutionLog::factory()->create([
        'execution_id' => $execution->id,
        'stream' => 'stdout',
        'line' => 'deploy started',
    ]);

    $approval = Approval::factory()->approved()->create([
        'execution_id' => $execution->id,
        'requested_by' => $operator->id,
        'approved_by' => $approver->id,
        'risk_level' => 2,
    ]);

    expect($execution->recipe->is($recipe))->toBeTrue()
        ->and($execution->target->is($target))->toBeTrue()
        ->and($execution->requestedBy->is($operator))->toBeTrue()
        ->and($execution->executionLogs()->whereKey($log->id)->exists())->toBeTrue()
        ->and($execution->approval->is($approval))->toBeTrue();

    expect($target->tags)->toBeArray()
        ->and($target->tags['env'])->toBe('prod')
        ->and($recipe->parameters)->toBeArray()
        ->and($recipe->parameters['branch']['enum'])->toContain('main')
        ->and($approval->approvedBy->is($approver))->toBeTrue()
        ->and($operator->requestedExecutions()->whereKey($execution->id)->exists())->toBeTrue();
});

it('enforces a single approval per execution', function (): void {
    $execution = Execution::factory()->create();

    Approval::factory()->create([
        'execution_id' => $execution->id,
    ]);

    expect(fn (): \App\Models\Approval => Approval::factory()->create([
        'execution_id' => $execution->id,
    ]))->toThrow(QueryException::class);
});
