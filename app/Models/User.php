<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    public $timestamps = false;

    protected $fillable = [
        'nameFirst', 'nameLast', 'username', 'emailAddress',
        'password', 'isActive', 'isHidden',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'isActive' => 'boolean',
        'isHidden' => 'boolean',
    ];

    public function getNameAttribute(): string
    {
        return "{$this->nameFirst} {$this->nameLast}";
    }

    /**
     * Verify password using the legacy hash algorithm from the old app.
     * Uses crypt() with SHA-256 and an MD5-derived salt.
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
}
