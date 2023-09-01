<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\File;

class AdminController extends Controller
{
    /**
     * Récupère la liste de tous les utilisateurs au format JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listUsers()
    {
        // Récupérez tous les utilisateurs depuis la base de données
        $users = User::all();

        return response()->json(['users' => $users]);
    }

    /**
     * Récupère les fichiers d'un utilisateur spécifique au format JSON.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewUserFiles($userId)
    {
        // Récupérez l'utilisateur en fonction de son ID
        $user = User::findOrFail($userId);

        // Récupérez les fichiers de cet utilisateur depuis la base de données
        $files = $user->files;

        return response()->json(['user' => $user, 'files' => $files]);
    }

    public function viewAllUserFiles()
{
    // Récupérez tous les utilisateurs depuis la base de données
    $users = User::all();

    // Initialisez un tableau vide pour stocker tous les fichiers
    $allFiles = [];

    // Parcourez tous les utilisateurs et récupérez leurs fichiers
    foreach ($users as $user) {
        // Récupérez les fichiers de cet utilisateur depuis la base de données
        $files = $user->files;

        // Ajoutez les fichiers de cet utilisateur au tableau $allFiles
        $allFiles[$user->id] = $files;
    }

    return response()->json(['allFiles' => $allFiles]);
}

}
