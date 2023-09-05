<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

