<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();
        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'first_name' => $data['first_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'address' => $data['address'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
            'siret_number' => $data['siret_number'],
            'available_space' => $data['available_space'],
            'password' => bcrypt($data['password']),
            'role' => 'user',
        ]);        
    
        $token = $user->createToken('main')->plainTextToken;
        return response()->json(['user' => $user,'token' => $token]);
    }
    

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'email ou mot de passe incorrect'
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user', 'token'));
    }

    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response([
            'message' => 'Utilisateur déconnecté'], 200);
    }
}
