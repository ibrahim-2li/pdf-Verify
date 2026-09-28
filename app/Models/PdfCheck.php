<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfCheck extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'is_modified',
        'message',
        'metadata',
    ];

    protected $casts = [
        'is_modified' => 'boolean',
        'metadata' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
