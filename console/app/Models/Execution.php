<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Execution extends Model
{
    /** @use HasFactory<\Database\Factories\ExecutionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'recipe_id',
        'target_id',
        'status',
        'requested_by',
        'params',
        'started_at',
        'finished_at',
        'exit_code',
        'error_summary',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'params' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'exit_code' => 'integer',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(Target::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function executionLogs(): HasMany
    {
        return $this->hasMany(ExecutionLog::class);
    }

    public function approval(): HasOne
    {
        return $this->hasOne(Approval::class);
    }
}
