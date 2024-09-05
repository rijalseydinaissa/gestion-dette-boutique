<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyCard extends Model
{
    protected $fillable = [
        'client_id',
        'surname',
        'telephone',
        'photo',
        'qrcode'
    ];

    protected $hidden = ['created_at', 'updated_at'];
        

    // Définir la relation avec le modèle Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

