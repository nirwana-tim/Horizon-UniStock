<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentLevel extends Model
{
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'deskripsi',
        'status',
    ];
}
