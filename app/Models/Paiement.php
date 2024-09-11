<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $table = 'paiements';

    // Les attributs qui peuvent être assignés en masse
    protected $fillable = [
        'dette_id',
        'montant',
        
    ];

    // Les relations avec les autres modèles

    /**
     * Get the debt that owns the payment.
     */
    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}
