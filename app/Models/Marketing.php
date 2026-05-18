<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    protected $fillable = [
        'name',
        'email',
        'is_active',
    ];

    // Tambahkan relasi atau metode lain sesuai kebutuhan
}
