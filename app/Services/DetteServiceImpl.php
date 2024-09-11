<?php

namespace App\Services;

use App\Jobs\ArchiveJob;
use App\Repository\DetteRepositoryInterface;
use App\Models\Paiement;
use App\Services\DetteServiceInterface;
use App\Models\Article;

class DetteServiceImpl implements DetteServiceInterface
{
    protected $detteRepository;
    public function __construct(DetteRepositoryInterface $detteRepository)
    {
        $this->detteRepository = $detteRepository;
    }
    public function addDebt(array $data)
    {
        if (empty($data['articles']) || !is_array($data['articles'])) {
            throw new \Exception("Le tableau des articles doit contenir au moins un article.");
        }
        $dette = $this->detteRepository->createDebt($data);
        $paiementMontant = isset($data['paiement']['montant']) ? $data['paiement']['montant'] : 0;
        if ($paiementMontant > 0) {
            if ($paiementMontant > $dette->montant) {
                throw new \Exception("Le montant du paiement ne peut pas être supérieur au montant de la dette.");
            }
            // dd($dette->id, $paiementMontant);
            Paiement::create([
                'dette_id' => $dette->id,
                'montant' => $paiementMontant,
                // dd($dette->id),
            ]);
            if ($paiementMontant = $dette->montant) {
                return response()->json(['message' => 'Paiement effectué'], 200);
            }
        }
        return $dette;

    }

            public function getAllDettes($statut = null)
        {
            return $this->detteRepository->getAllDebts($statut);
        }

        public function getDebtsById($id)
        {
            return $this->detteRepository->getDebtsById($id);
        }

        public function getArticlesByDette($id){
            return $this->detteRepository->getArticlesByDette($id);
        }
        
}