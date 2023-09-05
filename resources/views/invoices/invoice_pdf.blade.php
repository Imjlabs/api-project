<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facture</title>
    <style>
        /* Styles CSS pour la facture (personnalisez selon vos besoins) */
        body {
            font-family: Arial, sans-serif;
        }

        .invoice {
            margin: 20px;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .invoice-header p {
            font-size: 14px;
        }

        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details p {
            font-size: 14px;
            margin: 5px 0;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .invoice-table th {
            background-color: #f2f2f2;
        }

        .invoice-total {
            margin-top: 20px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="invoice-header">
            <h1>Facture</h1>
            <p>Date de la facture: {{ $invoice->invoice_date }}</p>
        </div>

        <div class="invoice-details">
            <p><strong>Informations du client:</strong></p>
            <p>Nom: {{ $invoice->client_first_name }} {{ $invoice->client_last_name }}</p>
            <p>Adresse: {{ $invoice->client_address }}</p>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Prix unitaire (HT)</th>
                    <th>Quantité</th>
                    <th>Total (HT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->invoice_description }}</td>
                    <td>{{ $invoice->unit_price }} €</td>
                    <td>{{ $invoice->quantity }}</td>
                    <td>{{ $invoice->total_ht }} €</td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-total">
            <p>Total (HT): {{ $invoice->total_ht }} €</p>
            <p>TVA ({{ $invoice->vat_rate }}%): {{ $invoice->vat_amount }} €</p>
            <p>Total TTC: {{ $invoice->total_ttc }} €</p>
        </div>

        <div class="invoice-footer">
            <p>Méthode de paiement: {{ $invoice->payment_method }}</p>
            <p>Date d'échéance: {{ $invoice->due_date }}</p>
        </div>
    </div>
</body>
</html>
