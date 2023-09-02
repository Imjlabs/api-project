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
            'file' => 'required|file|max:10240',
        ]);

        $user = Auth::user();

        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('uploads');

        $file = new File();
        $file->file_name = $uploadedFile->getClientOriginalName();
        $file->added_at = now();
        $file->file_path = $path;

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

        return response()->json(['files' => $files], 200);
    }
}

