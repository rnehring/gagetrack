<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    public $timestamps = false;

    protected $fillable = [
        'name', 'contact', 'code', 'email', 'phone', 'fax',
        'address', 'city', 'state', 'zipcode', 'supplierType', 'isActive',
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];
}
