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
        $request->validate([
            'file' => 'required', // Limitez la taille du fichier à 10 Mo (10240 Ko)
        ]);

        $user = Auth::user();
        $uploadedFile = $request->file('file');

        // Générez un nom de fichier unique
        $fileName = time() . '_' . $uploadedFile->getClientOriginalName();

        // Stockez le fichier dans un dossier spécifique pour chaque utilisateur
        $path = $uploadedFile->storeAs('uploads/' . $user->id, $fileName);

        $fileSize = $uploadedFile->getSize();

        // Créez une entrée de fichier dans la base de données
        $file = new File([
            'file_name' => $fileName,
            'added_at' => now(),
            'file_path' => $path,
            'file_size' => $fileSize, // Enregistrez la taille du fichier
        ]);

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
        $user = Auth::user();
        $file = $user->files()->find($fileId);

        if (!$file) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }

        // Supprimez le fichier du stockage
        Storage::delete($file->file_path);

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
        $user = Auth::user();
        $files = $user->files;

        $totalSize = $user->files()->sum('file_size');

        return response()->json(['files' => $files, 'total_size' => $totalSize], 200);
    }

    /**
     * Get a specific file for the authenticated user.
     *
     * @param  int  $fileId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function getFile($fileId)
    {
        $user = Auth::user();

        // Recherchez le fichier dans la base de données pour l'utilisateur authentifié
        $file = $user->files()->find($fileId);

        if (!$file) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }

        // Vérifiez si le fichier existe dans le stockage
        if (Storage::exists($file->file_path)) {
            // Si le fichier existe, renvoyez-le en tant que réponse
            return response()->stream(function () use ($file) {
                echo Storage::get($file->file_path);
            }, 200, [
                'Content-Type' => Storage::mimeType($file->file_path),
                'Content-Disposition' => 'inline; filename="' . $file->file_name . '"',
            ]);
        } else {
            return response()->json(['message' => 'Fichier introuvable dans le stockage'], 404);
        }
    }
}
