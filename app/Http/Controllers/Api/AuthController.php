<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use http\Env\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Notifications\VerifyEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;

class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();

        $emailVerificationToken = Str::random(60);

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
            'email_verified_token' => $emailVerificationToken,
        ]);

        $user->notify(new VerifyEmail($user));

        $token = $user->createToken('main')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token]);
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
            'message' => ['Utilisateur déconnecté']
        ], 200);
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verified_token', $token)->first();

        if (!$user) {

            return response()->json(['message' => 'Lien de vérification invalide.'], 400);
        }

        if ($user->email_verified_at !== null) {

            return response()->json(['message' => 'Votre e-mail a déjà été vérifié.'], 200);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Votre e-mail a été vérifié avec succès. Vous pouvez maintenant vous connecter.'], 200);
    }
}
