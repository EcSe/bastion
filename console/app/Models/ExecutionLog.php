<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecutionLog extends Model
{
    /** @use HasFactory<\Database\Factories\ExecutionLogFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'execution_id',
        'ts',
        'stream',
        'line',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ts' => 'datetime',
        ];
    }

    public function execution(): BelongsTo
    {
        return $this->belongsTo(Execution::class);
    }
}
