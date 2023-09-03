<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $totalFileCount = 0;

        foreach ($users as $user) {
            $files = $user->files;
            $fileCount = $files->count();
            $totalFileCount += $fileCount;
            $allFiles[$user->id] = [
                'user' => $user,
                'files' => $files,
                'file_count' => $fileCount,
            ];
        }

        return response()->json(['allFiles' => $allFiles, 'totalFileCount' => $totalFileCount], 200);
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
}
