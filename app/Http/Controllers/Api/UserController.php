<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */

     public function index()
     {
         // Récupérer la liste de tous les utilisateurs
         $users = User::all();
 
         return response()->json(['users' => $users], 200);
     }
     
    public function show(User $user)
    {
        // Vérifiez si l'utilisateur est connecté
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
        // Cette méthode est accessible uniquement pour l'utilisateur connecté
        if (Auth::user()->id === $user->id) {
            $data = $request->validated();

            // Vérification si l'email est unique (ignorer l'email actuel de l'utilisateur)
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
        // Cette méthode est accessible uniquement pour l'utilisateur connecté
        if (Auth::user()->id === $user->id) {
            $user->delete();

            return response("", 204);
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
    }
}
