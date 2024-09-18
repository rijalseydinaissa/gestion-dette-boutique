<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Demande extends Model
{
    protected $fillable = ['client_id', 'montant', 'status'];

    // Définir la relation avec les articles
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_demande') // Remplacez 'article_demande' par le nom de votre table pivot
            ->withPivot('quantite', 'prix')
            ->withTimestamps();
    }
}
