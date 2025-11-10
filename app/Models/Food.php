<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
