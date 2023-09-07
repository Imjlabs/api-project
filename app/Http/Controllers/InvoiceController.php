<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use PDF; // Importez la classe PDF depuis le package Laravel PDF

class InvoiceController extends Controller
{
    public function getUserInvoices()
{
    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Récupérer la liste des factures de l'utilisateur
    $invoices = Invoice::where('user_id', $user->id)->get();

    return response()->json(['invoices' => $invoices]);
}

public function downloadInvoice($invoice_id)
{
    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Rechercher la facture dans la base de données pour l'utilisateur connecté
    $invoice = $user->invoices()->findOrFail($invoice_id);

    // Obtenir le nom du fichier PDF de la facture
    $pdfFileName = $invoice->pdf_file_name;

    // Obtenir le chemin complet du fichier PDF
    $username = $user->id; // Ou utilisez votre méthode d'identification d'utilisateur
    $filePath = storage_path('app/uploads/' . $username . '/invoices/' . $pdfFileName);

    // Vérifier si le fichier existe
    if (!Storage::disk('local')->exists('uploads/' . $username . '/invoices/' . $pdfFileName)) {
        abort(404); // Le fichier n'existe pas, renvoyer une réponse 404
    }

    // Définir le type de contenu pour la réponse HTTP
    $headers = [
        'Content-Type' => 'application/pdf',
    ];

    // Télécharger le fichier PDF
    return response()->file($filePath, $headers);
}

public function getInvoice($invoiceId)
{
    // Récupérer l'utilisateur connecté
    $user = auth()->user();

    // Rechercher la facture par ID de la facture et l'ID de l'utilisateur
    $invoice = Invoice::where('id', $invoiceId)
        ->where('user_id', $user->id)
        ->first();

    if (!$invoice) {
        return response()->json(['message' => 'Facture non trouvée'], 404);
    }

    return response()->json(['invoice' => $invoice]);
}

}

