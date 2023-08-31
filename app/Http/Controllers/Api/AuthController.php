<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(SignupRequest $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'siret_number' => 'nullable|string|max:20',
            'available_space' => 'nullable|numeric',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token  = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' =>$token
        ]);
    }

    public function login(LoginRequest $request) {

        $credentials = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'siret_number' => 'nullable|string|max:20',
            'available_space' => 'nullable|numeric',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if (Auth::attempt([$credentials])) {
            return response([
                'Message' =>   'Email ou mot de passe incorrect',
            ], 204);
        }

        $user = Auth::user();

        $token = $user->createToken('main')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ], 200);

    }
    public function logout(Request $request) {
        $user = $request->user();

        $user->currentAccesToken()->delete();

        return response('Utilisateur bien déconnecté', 204);
    
    }
}
