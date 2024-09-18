<?php

namespace App\Repository;

use App\Models\Dette;
use App\Models\Article;
use App\Repository\DetteRepositoryInterface;
use App\Models\Client;
use DB;
use Illuminate\Support\Facades\Log;

class DetteRepositoryImpl implements DetteRepositoryInterface
{
   public function createDebt(array $data): Dette
{
    DB::beginTransaction();
    try {
        $client = Client::findOrFail($data['client_id']);
        if ($client->categorie_id == 2 && $data['montant'] > $client->max_montant) {
            throw new \Exception("Le montant de la dette ne peut pas être supérieur au max_montant pour un client Silver.");
        }
        $dette = Dette::create([
            'montant' => $data['montant'],
            'client_id' => $data['client_id'],
            'date_echeance' => $data['date_echeance']
        ]);
        foreach ($data['articles'] as $articleData) {
            $article = Article::findOrFail($articleData['articleId']);
            if ($article->qteStock < $articleData['qteVente']) {
                throw new \Exception("La quantité vendue est supérieure à la quantité en stock pour l'article ID: " . $articleData['articleId']);
            }
            $article->decrement('qteStock', $articleData['qteVente']);
            $dette->articles()->attach($article->id, [
                'qteVente' => $articleData['qteVente'],
                'prixVente' => $articleData['prixVente']
            ]);
        }
        DB::commit();
        return $dette;
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de la création de la dette : ' . $e->getMessage());
        throw $e; 
    }
}


    public function getAllDebts($statut = null)
        {
            $query = Dette::with('articles');

            if ($statut === 'solde') {
                $query->where('montant', '=', 0);
            } elseif ($statut === 'nonsolde') {
                $query->where('montant', '>', 0);
            }

            return $query->get();
        }

    

    public function getDebtsById($id){
        return Dette::with('articles')->find($id);
    }
    // public function getArticlesByDette($id){
    //     return Dette::find($id)->articles;
    // }
    //add paiement to debts
    //  public function addDettePaeiment($id, $data){
    //     $dette = Dette::findOrFail($id);
    //     $dette->montant -= $data['montant'];
    //     $dette->save();
    //     return $dette;
    //  }
    public function getArticlesByDette($id)
    {
        return Dette::findOrFail($id)->articles()->withPivot('qteVente', 'prixVente')->get();
    }
    public function getPaiementsByDette($id)
    {
        $dette = Dette::findOrFail($id);
        return $dette->paiements;
    }
  

}
