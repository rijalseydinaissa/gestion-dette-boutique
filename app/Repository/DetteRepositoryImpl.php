<?php

namespace App\Repository;

use App\Models\Dette;
use App\Models\Article;
use App\Repository\DetteRepositoryInterface;

class DetteRepositoryImpl implements DetteRepositoryInterface
{
    public function createDebt(array $data)
    {
        $dette = Dette::create([
            'montant' => $data['montant'],
            'client_id' => $data['clientId']
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
        return $dette;
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
    public function getArticlesByDette($id){
        return Dette::find($id)->articles;
    }
    //add paiement to debts
    //  public function addDettePaeiment($id, $data){
    //     $dette = Dette::findOrFail($id);
    //     $dette->montant -= $data['montant'];
    //     $dette->save();
    //     return $dette;
    //  }

     
  

}
