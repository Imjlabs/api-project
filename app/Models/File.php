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
        'user_id', // N'oubliez pas d'ajouter la colonne user_id ici
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
