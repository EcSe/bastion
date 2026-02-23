<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Target extends Model
{
    /** @use HasFactory<\Database\Factories\TargetFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'host',
        'port',
        'user',
        'auth_method',
        'key_path',
        'secret_ref',
        'tags',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'tags' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function executions(): HasMany
    {
        return $this->hasMany(Execution::class);
    }
}
