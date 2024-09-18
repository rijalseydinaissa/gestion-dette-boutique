<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Dette;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['libelle', 'prix', 'qteStock'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $dates = ['deleted_at'];
    public function scopeDisponible($query, $disponible)
    {
        if ($disponible === 'oui') {
            return $query->where('qteStock', '>', 0);
        } elseif ($disponible === 'non') {
            return $query->where('qteStock', '=', 0);
        }
        return $query;
    }
    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }
    public function demandes(): BelongsToMany
    {
        return $this->belongsToMany(Demande::class, 'article_demande') // Remplacez 'article_demande' par le nom de votre table pivot
            ->withPivot('quantite', 'prix')
            ->withTimestamps();
    }
}
