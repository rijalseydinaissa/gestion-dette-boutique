<?php

namespace App\Services;

use App\Models\Demande;
use Illuminate\Support\Facades\DB;

class DemandeService
{
    public function creerDemande($client_id, $articleData)
    {
        try {
            DB::beginTransaction();
            $montant_total = 0;
            foreach ($articleData as $article) {
                $montant_total += $article['quantite'] * $article['prix'];
            }
            $demande = Demande::create([
                'client_id' => $client_id,
                'montant_total' => $montant_total,
                'status' => 'en attente',
            ]);
            foreach ($articleData as $article) {
                $demande->articles()->attach($article['id'], [
                    'quantite' => $article['quantite'],
                    'prix' => $article['prix'],
                ]);
            }
            DB::commit();
            return $demande->load('articles');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}