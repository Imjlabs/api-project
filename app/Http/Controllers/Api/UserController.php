<?php

namespace App\Http\Controllers\Api;

use App\Models\File;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AccountDeleted;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Invoice;
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

                 $userWithStorageInfo = User::with('files')->find($user->id);
     
                 return new UserResource($userWithStorageInfo);
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
                $userWithStorageInfo = User::with('files')->find($user->id);
    
                return new UserResource($userWithStorageInfo);
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

        if (Auth::user()->id === $user->id) {
            
            $files = File::where('user_id', $user->id)->get();
            
            foreach ($files as $file) {
                Storage::delete($file->path); // Supprimez le fichier en utilisant son chemin de stockage
            }
            
            $invoices = Invoice::where('user_id', $user->id)->get();
            
            // foreach ($invoices as $invoice) {
            //     Storage::delete($invoice->file_path); // Supprimez le fichier lié à la facture en utilisant son chemin de stockage
            // }
            
            File::where('user_id', $user->id)->delete();
            Invoice::where('user_id', $user->id)->delete();
            $admin = User::where('email', 'admin@architecturae.com')->first(); 
            
            $user->notify(new AccountDeleted);
            
            $admin->notify(new UserDeletedNotification());
            
            $user->delete();
            return response()->json("Votre compte a bien été supprimé ! Ravi de vous avoir compté parmi nos utilisateurs", 200);
        } else {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }
}

    /**
     * Récupère la taille totale des fichiers stockés dans le dossier de l'utilisateur connecté.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserStorageSize()
    {
        $user = Auth::user(); // Récupère l'utilisateur actuellement authentifié
    
        if (!$user) {
            return response()->json(['error' => 'Aucun utilisateur connecté.'], 401);
        }
    
        // Récupérez le chemin du dossier de stockage de l'utilisateur (vous devrez ajuster cela en fonction de votre structure de stockage)
        $storagePath = 'users/' . $user->id;
    
        // Récupérez la liste des fichiers dans le stockage de l'utilisateur
        $files = Storage::files($storagePath);
    
        // Initialisez une variable pour stocker la taille totale
        $totalSize = 0;
    
        // Bouclez à travers les fichiers pour calculer la taille totale
        foreach ($files as $file) {
            $totalSize += Storage::size($file);
        }
    
        return response()->json(['total_size' => $totalSize], 200);
    }
    

    /**
     * Formatage de la taille en unités lisibles par l'homme (octets, Ko, Mo, Go, etc.).
     *
     * @param  int  $bytes
     * @return string
     */
    private function formatSizeUnits($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function addStorageSpace(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = auth()->user();

        // Ajouter 20 Go d'espace de stockage (1 Go = 1024 Mo)
        $user->increment('available_space', 20 * 1024);

        // Retourner une réponse JSON pour indiquer que l'espace de stockage a été ajouté avec succès
        return response()->json(['message' => '20 Go d\'espace de stockage ont été ajoutés avec succès', 'user' => $user]);
    }
}

