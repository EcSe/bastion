<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    /** @use HasFactory<\Database\Factories\ApprovalFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'execution_id',
        'requested_by',
        'status',
        'risk_level',
        'summary',
        'expires_at',
        'approved_at',
        'approved_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'risk_level' => 'integer',
            'expires_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function execution(): BelongsTo
    {
        return $this->belongsTo(Execution::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
