<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configurations';
    public $timestamps = false;

    protected $fillable = ['name', 'value'];

    public static function getValue(string $name): ?string
    {
        $config = static::where('name', $name)->first();
        return $config ? $config->value : null;
    }

    public static function setValue(string $name, string $value): void
    {
        static::where('name', $name)->update(['value' => $value]);
    }
}
