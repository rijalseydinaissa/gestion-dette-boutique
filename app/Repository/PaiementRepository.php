<?php
namespace App\Repository;

use App\Models\Paiement;
use App\Models\Dette;

class PaiementRepository 
{
    public function createPaiement(array $data)
    {
        // Créer un nouveau paiement
        return Paiement::create([
            'dette_id' => $data['dette_id'],
             'montant' => $data['montant'],
        ]);
    }

    public function getDetteById(int $id)
    {
        // Récupérer la dette par ID
        return Dette::findOrFail($id);
    }

    public function updateDette(int $id, array $data)
    {
        $dette = Dette::findOrFail($id);
        $dette->update($data);
        return $dette;
    }
}
