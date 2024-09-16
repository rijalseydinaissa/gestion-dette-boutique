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
        'date_echeance',
        
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    // Appends: inclure les attributs transitoires dans les résultats des requêtes
    protected $appends = [
        'montant_du',
        'montant_restant',
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
    public function getMontantDuAttribute()
    {
        $montantDu = 0;
        foreach ($this->articles as $article) {
            $montantDu += $article->pivot->qteVente * $article->pivot->prixVente;
        }
        return $montantDu;
    }
    /**
     * Accessor pour calculer le montant restant.
     *
     * @return float
     */
    public function getMontantRestantAttribute()
    {
        $montantPaye = $this->paiements->sum('montant');
        return $this->montant_du - $montantPaye;
    }
    public function scopeSoldes($query)
    {
        return $query->whereHas('paiements', function ($q) {
            $q->select('dette_id')
                ->groupBy('dette_id')
                ->havingRaw('SUM(montant) = dettes.montant');
        });
    }

    public function scopeNonSoldes($query, $flag = true)
    {
        if ($flag) {
            return $query->where(function ($q) {
                $q->doesntHave('paiements')
                ->orWhereHas('paiements', function ($q) {
                    $q->select('dette_id')
                        ->groupBy('dette_id')
                        ->havingRaw('SUM(montant) < dettes.montant');
                });
            });
        }
    }
    
}
