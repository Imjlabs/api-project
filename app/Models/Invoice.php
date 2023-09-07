<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_first_name',
        'client_last_name',
        'client_address',
        'company_name',
        'company_address',
        'company_siret',
        'invoice_date',
        'invoice_description',
        'unit_price',
        'quantity',
        'vat_rate',
        'vat_amount',
        'total_ht',
        'total_ttc',
        'payment_method',
        'due_date',
        'notes',
        'pdf_file_name', // Ajoutez le champ pour le nom du fichier PDF
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

