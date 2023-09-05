<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\User;
use App\Models\Invoice;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        try {
            // Initialisez Stripe avec votre clé secrète
            Stripe::setApiKey(config('services.stripe.secret'));

            $data = $request->validate([
                'amount' => 'required|integer',
                'currency' => 'required|string',
            ]);

            $amount = $data['amount'];
            $currency = $data['currency'];

            // Récupérez l'ID de l'utilisateur connecté
            $userId = Auth::id();

            // Créez le paiement intent en incluant l'ID de l'utilisateur dans les métadonnées
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'metadata' => [
                    'user_id' => $userId, // Inclure l'ID de l'utilisateur dans les métadonnées
                ],
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                "test" => "test"
            ], 200);
        } catch (\Exception $error) {
            \Log::error($error);
            \Log::debug('Client Secret reçu : ' . $paymentIntent->client_secret);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la création du paiement intent.',
                "test" => "test"
            ], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = auth()->user();

        // Créer une nouvelle instance de la facture
        $invoice = new Invoice();

        // Remplir les champs de la facture avec les informations de l'utilisateur
        $invoice->client_first_name = $user->first_name;
        $invoice->client_last_name = $user->name;
        $invoice->client_address = $user->address;
        $invoice->user_id = $user->id;

        // Remplir les autres champs en dur
        $invoice->company_name = 'Artichecturae';
        $invoice->company_address = '28 rue claude tillier';
        $invoice->company_siret = '125 485698 8563';
        $invoice->invoice_date = now();
        $invoice->invoice_description = 'Achat de 20 go de stockage';
        $invoice->unit_price = 16; // Prix unitaire hors taxe
        $invoice->quantity = 1; // Quantité
        $invoice->vat_rate = 20; // Taux de TVA en pourcentage

        // Calculer les montants HT, TVA et TTC
        $invoice->total_ht = $invoice->unit_price * $invoice->quantity;
        $invoice->vat_amount = ($invoice->total_ht * $invoice->vat_rate) / 100;
        $invoice->total_ttc = $invoice->total_ht + $invoice->vat_amount;

        // Autres informations (à ajuster selon vos besoins)
        $invoice->payment_method = 'Chèque';
        $invoice->due_date = now()->addDays(30);
        $invoice->notes = 'Remarques facultatives';

        // Enregistrer la facture dans la base de données
        $invoice->save();

        // Générer le contenu de la facture au format PDF
        $pdf = PDF::loadView('invoices.invoice_pdf', compact('invoice'));

        // Générer un nom unique pour le fichier PDF
        $pdfFileName = 'invoice_' . $user->id . '_' . uniqid() . '.pdf';

        // Obtenir le nom d'utilisateur (par exemple, l'email ou le nom d'utilisateur)
        $username = $user->id; // Vous pouvez ajuster ceci en fonction de la manière dont vous identifiez l'utilisateur

        // Créer le répertoire de destination s'il n'existe pas
        Storage::makeDirectory('uploads/' . $username . '/invoices');

        // Stocker le fichier PDF dans le répertoire storage/app/uploads/{nom_utilisateur}/invoices
        Storage::put('uploads/' . $username . '/invoices/' . $pdfFileName, $pdf->output());

        // Mettre à jour l'espace de stockage de l'utilisateur (ajouter 20 Go)
        $user->increment('available_space', 20 * 1024); // 1 Go = 1024 Mo

        // Retourner la facture générée en réponse HTTP
        return response()->json(['message' => 'Facture créée et espace de stockage mis à jour avec succès', 'invoice' => $invoice]);
    }


}
