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
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->default(3); // 3 pour Bronze par défaut
            $table->decimal('max_montant', 10, 2)->nullable(); // Nullable pour les catégories autres que Silver
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('categorie_id');
            $table->dropColumn('max_montant');
        });
    }
};
