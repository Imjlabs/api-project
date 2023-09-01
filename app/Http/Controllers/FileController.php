<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Upload a file for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request)
    {
        // Valider le téléversement de fichier
        $request->validate([
            'file' => 'required|file|max:10240', // Taille maximale de 10 Mo
        ]);

        // Obtenir l'utilisateur connecté
        $user = Auth::user();

        // Stocker le fichier dans le répertoire 'uploads'
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads');

        // Créer une entrée en base de données pour le fichier associé à l'utilisateur connecté
        $file = new File();
        $file->file_name = $uploadedFile->getClientOriginalName();
        $file->added_at = now(); // Date d'ajout actuelle
        $file->file_path = $path;

        // Associer le fichier à l'utilisateur
        $user->files()->save($file);

        return response()->json(['message' => 'Fichier téléversé avec succès', 'file' => $file], 200);
    }

    /**
     * Delete a file for the authenticated user.
     *
     * @param  int  $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile($fileId)
    {
        // Obtenir l'utilisateur connecté
        $user = Auth::user();

        // Rechercher le fichier par ID associé à l'utilisateur connecté
        $file = $user->files()->find($fileId);

        if (!$file) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }

        // Supprimer le fichier du stockage
        Storage::delete($file->file_path);

        // Supprimer l'entrée du fichier de la base de données
        $file->delete();

        return response()->json(['message' => 'Fichier supprimé avec succès'], 200);
    }

    /**
     * Get a list of files for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFiles()
    {
        // Obtenir l'utilisateur connecté
        $user = Auth::user();

        // Récupérer la liste des fichiers associés à l'utilisateur
        $files = $user->files;

        return response()->json(['files' => $files], 200);
    }
}

