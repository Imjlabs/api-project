<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Récupère la liste de tous les utilisateurs au format JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function listUsers()
    {

        $users = User::where('role', 'user')->get();
        $allFiles = [];
        $totalFileCount = 0;
        $totalFileSize = 0; // Taille totale des fichiers
        $totalAvailableSpace = 0; // Taille disponible totale
        $totalSpaceUsed = 0;
        
        foreach ($users as $user) {
            $files = $user->files;
            $fileCount = $files->count();
            $totalFileCount += $fileCount;
    
            // Calculer la taille totale des fichiers pour cet utilisateur
            $userTotalFileSize = $files->sum('file_size');
            $totalFileSize += $userTotalFileSize;
    
            // Ajouter la taille disponible de cet utilisateur à la taille disponible totale
            $totalAvailableSpace += $user->available_space;
    
            $allFiles[$user->id] = [
                'user' => $user,
                'files' => $files,
                'file_count' => $fileCount,
                'user_total_file_size' => $userTotalFileSize,
            ];
    
            // Calculer l'espace utilisé par cet utilisateur
            $spaceUsed = $userTotalFileSize / 1024; // Convertir en Mo
            $totalSpaceUsed += $spaceUsed;
        }

    return response()->json([
        'allFiles' => $allFiles,
        'totalFileCount' => $totalFileCount,
        'totalFileSize' => $totalFileSize, // Espace restant
    ], 200);
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
        $totalFileCount = 0;
        $totalFileSize = 0; // Taille totale des fichiers
        $totalAvailableSpace = 0; // Taille disponible totale
        $totalSpaceUsed = 0; // Espace total utilisé
    
        foreach ($users as $user) {
            $files = $user->files;
            $fileCount = $files->count();
            $totalFileCount += $fileCount;
    
            // Calculer la taille totale des fichiers pour cet utilisateur
            $userTotalFileSize = $files->sum('file_size');
            $totalFileSize += $userTotalFileSize;
    
            // Ajouter la taille disponible de cet utilisateur à la taille disponible totale
            $totalAvailableSpace += $user->available_space;
    
            $allFiles[$user->id] = [
                'user' => $user,
                'files' => $files,
                'file_count' => $fileCount,
                'user_total_file_size' => $userTotalFileSize,
            ];
    
            // Calculer l'espace utilisé par cet utilisateur
            $spaceUsed = $userTotalFileSize / 1024; // Convertir en Mo
            $totalSpaceUsed += $spaceUsed;
        }
    
        // Calculer l'espace restant
        $totalSpaceRemaining = $totalAvailableSpace - $totalSpaceUsed;
    
        // Ajouter l'espace restant dans la réponse JSON
        return response()->json([
            'allFiles' => $allFiles,
            'totalFileCount' => $totalFileCount,
            'totalFileSize' => $totalFileSize,
            'totalAvailableSpace' => $totalAvailableSpace,
            'totalSpaceUsed' => $totalSpaceUsed,
            'totalSpaceRemaining' => $totalSpaceRemaining, // Espace restant
        ], 200);
    }
    
    public function totalUploadedFilesCount()
    {
        $totalFileCount = File::count();

        return response()->json(['total_uploaded_files' => $totalFileCount], 200);
    }

    public function uploadedFilesTodayCount()
    {
        $today = Carbon::today();
        $uploadedFilesCountToday = File::whereDate('created_at', $today)->count();

        return response()->json(['uploaded_files_today' => $uploadedFilesCountToday], 200);
    }

    public function filesPerClient()
    {
        $users = User::all();
        $filesPerClient = [];

        foreach ($users as $user) {
            $filesCount = $user->files->count();
            $filesPerClient[$user->name] = $filesCount;
        }

        return response()->json(['files_per_client' => $filesPerClient], 200);
    }

/**
 * Récupère un fichier d'un utilisateur spécifique au format JSON.
 *
 * @param  int  $userId
 * @param  int  $fileId
 * @return \Illuminate\Http\JsonResponse
 */


public function getFile($fileId)
{
    $file = File::findOrFail($fileId);


    // Vous pouvez également ajouter des vérifications de sécurité supplémentaires ici, comme les autorisations de l'administrateur

    // Récupérez le chemin du fichier
    $filePath = $file->file_path;

    // Vérifiez si le fichier existe
    if (!Storage::exists($filePath)) {
        return response()->json(['error' => 'Le fichier n\'existe pas.'], 404);
    }

    // Obtenez le nom du fichier
    $fileName = pathinfo($filePath, PATHINFO_FILENAME);

    // Définissez les en-têtes de la réponse pour le téléchargement
    return response()->download(storage_path('app/' . $filePath), $fileName);
}

public function getUserStorageSize($userId)
    {
        $user = User::findOrFail($userId);

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

        // Formatez la taille totale pour l'afficher en octets, Ko, Mo, Go, etc., en fonction de vos besoins
        $formattedSize = $this->formatSizeUnits($totalSize);

        return response()->json(['total_size' => $formattedSize], 200);
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
}

