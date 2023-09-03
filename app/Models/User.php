<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\File;
use App\Events\UserDeleting;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail 
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'first_name',
        'email',
        'phone_number',
        'address',
        'city',
        'postal_code',
        'siret_number',
        'available_space',
        'password',
        'role',
        'email_verified_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'deleting' => UserDeleting::class,
    ];

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function usedSpace(): int
    {
        return $this->files()->sum('file_size');
    }

    public function remainingSpace(): int
    {
        return $this->available_space - $this->usedSpace();
    }
}
