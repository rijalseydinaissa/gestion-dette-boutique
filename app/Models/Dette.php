<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;
    protected $table = 'dettes';

    // Définir les attributs modifiables en masse
    protected $fillable = [
        'montant',
        'client_id',
    ];

    protected $hidden = [
            //  'password',
            'created_at',
            'updated_at',
    ];

    // Définir les relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
        ->withPivot('qteVente', 'prixVente')
        ->withTimestamps();
    }
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
