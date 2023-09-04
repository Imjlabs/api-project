<?php

namespace App\Http\Controllers;
    
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{


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
    
                return response()->json(['client_secret' => $paymentIntent->client_secret], 200);
            } catch (\Exception $error) {
                \Log::error($error);
                return response()->json(['error' => 'Une erreur est survenue lors de la création du paiement intent.'], 500);
            }
        }
    }
}    
