<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'nama',
        'no_wa',
        'email',
        'nama_lembaga',
    ];
}
