<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperationLog extends Model
{
    protected $fillable = [
        'operation_type',
        'input_filename',
        'output_filename',
        'operation_details',
        'execution_time',
        'status',
        'error_message',
        'file_path',
        'file_size',
    ];

    protected $casts = [
        'operation_details' => 'array',
        'execution_time' => 'decimal:4',
        'downloaded_at' => 'datetime',
    ];

    public function fileOperations(): HasMany
    {
        return $this->hasMany(FileOperation::class);
    }

    public function getOperationTypeDisplayAttribute(): string
    {
        return match ($this->operation_type) {
            'reverse' => 'リバース操作',
            'copy' => 'コピー操作',
            'duplicate' => '重複操作',
            'replace' => '置換操作',
            default => $this->operation_type,
        };
    }
}
