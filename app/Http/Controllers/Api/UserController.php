<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AccountDeleted;
use App\Http\Requests\UpdateUserRequest;
use App\Notifications\UserDeletedNotification;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */

     
    public function index(User $user)
    {
        if (Auth::check()) {
            $authenticatedUser = Auth::user();

            if ($authenticatedUser->id === $user->id) {
                return new UserResource($user);
            } else {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
        } else {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }
    }
    public function show(User $user)
    {
        if (Auth::check()) {
            $authenticatedUser = Auth::user();

            if ($authenticatedUser->id === $user->id) {
                return new UserResource($user);
            } else {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }
        } else {
            return response()->json(['message' => 'Utilisateur non connecté'], 401);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateUserRequest $request
     * @param \App\Models\User                     $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if (Auth::user()->id === $user->id) {
            $data = $request->validated();

            if (isset($data['email']) && $data['email'] !== $user->email && User::where('email', $data['email'])->exists()) {
                return response()->json(['message' => 'L\'email est déjà utilisé par un autre utilisateur'], 409);
            }

            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }
            $user->update($data);

            return new UserResource($user);
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Vérifiez si l'utilisateur actuellement authentifié est le même que l'utilisateur que vous souhaitez supprimer
        if (Auth::user()->id === $user->id) {
            // Supprimez tous les fichiers de l'utilisateur
            File::where('user_id', $user->id)->delete();
    
            // Supprimez l'utilisateur lui-même
            $user->delete();
            
            $admin = User::where('email', 'admin@example.com')->first(); 
            // Remplacez par l'administrateur réel ou fictif
            
            // Envoyez une notification ou un message de confirmation, si nécessaire
            $user->notify(new AccountDeleted);

            $admin->notify(new UserDeletedNotification($user));
            
            return response()->json("Votre compte a bien été supprimé ! Ravi de vous avoir compté parmi nos utilisateurs", 200);
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
}
}