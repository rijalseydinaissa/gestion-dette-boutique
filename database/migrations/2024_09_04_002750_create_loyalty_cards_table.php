<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoyaltyCardsTable extends Migration
{
    public function up()
    {
        Schema::create('loyalty_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('surname');
            $table->text('photo'); // Utilisé pour stocker l'image encodée en base64
            $table->text('qr_code'); // Utilisé pour stocker le code QR encodé en base64
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loyalty_cards');
    }
}
