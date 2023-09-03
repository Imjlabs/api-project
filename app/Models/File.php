<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class File extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'added_at',
        'file_path',
        'user_id',
        'file_size'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
