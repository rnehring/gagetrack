<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metadata extends Model
{
    protected $table = 'metadata';
    public $timestamps = false;

    protected $fillable = ['category', 'value', 'description'];

    public static function byCategory(string $category)
    {
        return static::where('category', $category)->orderBy('value')->get();
    }

    public static function categories()
    {
        return static::select('category')->distinct()->orderBy('category')->pluck('category');
    }
}
