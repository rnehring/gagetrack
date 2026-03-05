<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'nameFirst', 'nameLast', 'username', 'emailAddress',
        'password', 'isActive', 'isActive_master', 'isHidden',
    ];

    protected $hidden = ['password', 'oldPassword'];

    protected $casts = [
        'isActive'        => 'boolean',
        'isActive_master' => 'boolean',
        'isHidden'        => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return "{$this->nameFirst} {$this->nameLast}";
    }

    /**
     * Legacy hash algorithm from the old app.
     * Uses crypt() SHA-256 with an MD5-derived salt.
     */
    public static function legacyPasswordHash(string $username, string $password): string
    {
        $salt = '$5$';
        $salt .= substr(md5(strtolower($username)), 0, 16);
        $salt .= '$';
        return substr(crypt($password, $salt), 20);
    }

    public function verifyPassword(string $password): bool
    {
        $hash = self::legacyPasswordHash($this->username, $password);
        return $this->password === $hash;
    }

    /**
     * A user is loginable only when both flags are active.
     */
    public function getIsLoginableAttribute(): bool
    {
        return (bool) $this->isActive && (bool) $this->isActive_master;
    }

    public function metadata(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserMetadata::class, 'id', 'id');
    }
}
