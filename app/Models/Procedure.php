<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $table = 'procedures';
    public $timestamps = false;

    protected $fillable = ['name', 'description'];
}
