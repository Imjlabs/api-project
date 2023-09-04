<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->string('recipient_name');
            $table->decimal('total_amount', 10, 2); // Utilisez la précision appropriée
            $table->string('payment_method');
            $table->date('due_date');
            $table->string('payment_status');
            $table->json('provider_info'); // Utilisez le type de colonne JSON pour stocker les informations du fournisseur
            $table->timestamps();

            // Clé étrangère vers la table des utilisateurs
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
