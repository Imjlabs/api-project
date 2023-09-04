<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Récupère la liste de toutes les factures pour l'utilisateur connecté.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Récupérez l'utilisateur connecté
        $user = Auth::user();

        // Récupérez les factures associées à cet utilisateur
        $invoices = $user->invoices;

        return response()->json($invoices, 200);
    }

    /**
     * Récupère une facture spécifique en fonction de son ID pour l'utilisateur connecté.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Récupérez l'utilisateur connecté
        $user = Auth::user();

        // Récupérez la facture associée à cet utilisateur par ID
        $invoice = $user->invoices()->findOrFail($id);

        return response()->json($invoice, 200);
    }
}
