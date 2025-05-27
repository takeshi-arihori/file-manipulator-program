<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileOperation extends Model
{
    protected $fillable = [
        'operation_log_id',
        'original_filename',
        'stored_filename',
        'file_path',
        'operation_directory',
        'file_size',
        'mime_type',
        'file_content_preview',
        'is_downloaded',
        'downloaded_at',
    ];

    protected $casts = [
        'is_downloaded' => 'boolean',
        'downloaded_at' => 'datetime',
    ];

    public function operationLog(): BelongsTo
    {
        return $this->belongsTo(OperationLog::class);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
