<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Companion table to users — mirrors the user's id as PK.
 * Tracks isActive / isHidden state separate from the main users table.
 * (Legacy schema pattern from original gagetrack app.)
 */
class UserMetadata extends Model
{
    protected $table = 'usermetadata';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['id', 'isActive', 'isHidden'];

    protected $casts = [
        'isActive' => 'boolean',
        'isHidden' => 'boolean',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}
