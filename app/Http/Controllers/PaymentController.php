<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\User;
use App\Models\Invoice;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;


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

            // Créez le paiement intent
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
            ]);


            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                "test" => "test"
            ], 200);
        } catch (\Exception $error) {
            \Log::error($error);
            return response()->json([
                'error' => 'Une erreur est survenue lors de la création du paiement intent.',
                "test" => "test"
            ], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        try {
            // Initialisez Stripe avec votre clé secrète
            Stripe::setApiKey(config('services.stripe.secret'));

            $data = $request->validate([
                'client_secret' => 'required|string',
                // Autres validations nécessaires pour les détails de paiement
            ]);

            $clientSecret = $data['client_secret'];

            // Confirmez la transaction Stripe
            $paymentIntent = PaymentIntent::retrieve($clientSecret);

            // Récupérez l'utilisateur lié à cette transaction
            $user = User::findOrFail($paymentIntent->metadata->user_id);

            // Augmentez l'espace de stockage de l'utilisateur (par exemple, +20 Go)
            $user->available_space += 20 * 1024 * 1024; // 20 Go en octets
            $user->save();

            // Créez une facture pour la transaction
            $invoice = new Invoice();
            $invoice->user_id = $user->id;
            $invoice->amount = $paymentIntent->amount / 100; // Convertir le montant de cents à la devise
            $invoice->save();

            // Stockez la facture dans un dossier spécifique (par exemple, "invoices/user_id")
            $invoice->store("invoices/{$user->id}");

            // Répondez au client avec une réponse JSON de succès
            return response()->json(['message' => 'Paiement confirmé avec succès', 'invoice_id' => $invoice->id], 200);
        } catch (\Exception $error) {
            \Log::error($error);
            return response()->json(['error' => 'Une erreur est survenue lors de la confirmation du paiement.'], 500);
        }
    }

    public function confirmPayment(Request $request)
{
    try {
        // Initialisez Stripe avec votre clé secrète
        Stripe::setApiKey(config('services.stripe.secret'));

        $data = $request->validate([
            'client_secret' => 'required|string',
            // Autres validations nécessaires pour les détails de paiement
        ]);

        $clientSecret = $data['client_secret'];

        // Confirmez la transaction Stripe
        $paymentIntent = PaymentIntent::retrieve($clientSecret);

        // Récupérez l'utilisateur lié à cette transaction
        $user = User::findOrFail($paymentIntent->metadata->user_id);

        // Augmentez l'espace de stockage de l'utilisateur (par exemple, +20 Go)
        $user->available_space += 20 * 1024 * 1024; // 20 Go en octets
        $user->save();

        // Générez un nom de facture unique (par exemple, en utilisant un timestamp)
        $invoiceName = 'invoice_' . time() . '.pdf'; // Exemple de nom unique avec un timestamp

        // Créez une facture pour la transaction
        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->amount = $paymentIntent->amount / 100; // Convertir le montant de cents à la devise
        $invoice->file_name = $invoiceName; // Attribuez le nom de facture unique
        $invoice->save();

        // Stockez la facture dans un dossier spécifique avec le nom unique
        $invoice->storeAs("invoices/{$user->id}", $invoiceName);

        // Répondez au client avec une réponse JSON de succès
        return response()->json(['message' => 'Paiement confirmé avec succès', 'invoice_id' => $invoice->id], 200);
    } catch (\Exception $error) {
        \Log::error($error);
        return response()->json(['error' => 'Une erreur est survenue lors de la confirmation du paiement.'], 500);
    }
}

}
