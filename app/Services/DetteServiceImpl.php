<?php

namespace App\Services;

use App\Jobs\ArchiveJob;
use App\Repository\DetteRepositoryInterface;
use App\Models\Paiement;
use App\Services\DetteServiceInterface;
use App\Models\Article;
use App\Models\Dette;

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
            Paiement::create([
                'dette_id' => $dette->id,
                'montant' => $paiementMontant,
            ]);
            if ($paiementMontant == $dette->montant) {
                return response()->json([
                    'message' => 'Paiement effectué',
                    'dette' => $dette,
                ], 200);
            }
        }
        return response()->json([
            'message' => 'Dette ajoutée avec succès',
            'dette' => $dette,
        ], 200);
    }
    


            public function getAllDettes($statut = null)
            {
                return $this->detteRepository->getAllDebts($statut);
            }

        public function getDebtsById($id)
            {
                return $this->detteRepository->getDebtsById($id);
            }

        // public function getArticlesByDette($id){
        //     return $this->detteRepository->getArticlesByDette($id);
        // }
        public function getArticlesByDette($id)
        {
            return $this->detteRepository->getArticlesByDette($id);
        }
    public function getPaiementsByDette($id)
        {
            return Dette::with('paiements')->find($id);
        }
}