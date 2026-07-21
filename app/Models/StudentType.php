<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentType extends Model
{
    protected $fillable = [
        'kode',
        'deskripsi',
        'status',
    ];
}
