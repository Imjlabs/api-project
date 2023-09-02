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
        'first_name', // Champ supplémentaire
        'email',
        'phone_number', // Champ supplémentaire
        'address', // Champ supplémentaire
        'city', // Champ supplémentaire
        'postal_code', // Champ supplémentaire
        'siret_number', // Champ supplémentaire
        'available_space', // Champ supplémentaire
        'password',
        'role',
        'email_verified_token',
        'email_verified_at' // Assurez-vous que 'role' est inclus ici
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
}
