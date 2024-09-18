<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // Création de la table demandes
public function up()
{
    Schema::create('demandes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained()->onDelete('cascade');
        $table->decimal('montant', 10, 2); // Exemple de montant
        $table->string('status'); // Assurez-vous que cette ligne est présente
        $table->timestamps();
    });
}

// Suppression de la table demandes
public function down()
{
    Schema::dropIfExists('demandes');
}

};
