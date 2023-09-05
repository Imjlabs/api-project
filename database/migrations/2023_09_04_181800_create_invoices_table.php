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
            $table->string('client_first_name');
            $table->string('client_last_name');
            $table->string('client_address');
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_siret');
            $table->date('invoice_date');
            $table->string('invoice_description');
            $table->decimal('unit_price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total_ht', 10, 2);
            $table->decimal('vat_rate', 5, 2);
            $table->decimal('vat_amount', 10, 2);
            $table->decimal('total_ttc', 10, 2);
            $table->string('payment_method');
            $table->date('due_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
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
