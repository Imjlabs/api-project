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
        $user = User::findOrFail($userId);

        $files = $user->files;

        return response()->json(['user' => $user, 'files' => $files]);
    }

    public function viewAllUserFiles()
    {
        $users = User::all();
        $allFiles = [];
        $totalFileCount = 0; // Initialiser le compteur total de fichiers

        foreach ($users as $user) {
            $files = $user->files;
            $fileCount = $files->count(); // Compter le nombre de fichiers
            $totalFileCount += $fileCount; // Ajouter au compteur total
            $allFiles[$user->id] = [
                'user' => $user,
                'files' => $files,
                'file_count' => $fileCount,
            ];
        }

        return response()->json(['allFiles' => $allFiles, 'totalFileCount' => $totalFileCount], 200);
    }
}
