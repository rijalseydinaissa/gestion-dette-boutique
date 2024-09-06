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
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('upload_failed')->default(false); // Définit si le téléchargement a échoué
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('upload_failed');
    });
}

};
