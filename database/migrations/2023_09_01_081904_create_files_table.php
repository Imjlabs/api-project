<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->timestamp('added_at');
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable();
            $table->unsignedBigInteger('user_id'); // Nouvelle colonne pour l'ID de l'utilisateur
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users'); // Clé étrangère vers la table des utilisateurs
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};
