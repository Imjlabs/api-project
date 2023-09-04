<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'invoice_date',
        'recipient_name',
        'total_amount',
        'payment_method',
        'due_date',
        'payment_status',
        'provider_info', // Les informations du fournisseur ou de l'émetteur de la facture
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
