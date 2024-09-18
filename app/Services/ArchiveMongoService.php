<?php

namespace App\Services;

use App\Models\MongoDette;
use App\Models\Dette;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ArchiveMongoService implements ArchiveDetteInterface
{
    protected $mongoDette;

    public function __construct()
    {
        $this->mongoDette = new MongoDette();
    }

    public function archiveSettledDebts($request = null)
    {
        $settledDebts = Dette::nonSoldes()->get();
        foreach ($settledDebts as $dette) {
            $detteData = [
                'client_id' => $dette->client_id,
                'montant' => $dette->montant,
                'dette_id' => $dette->id,
                'articles' => $dette->articles->toArray(),
                'paiements' => $dette->paiements->toArray(),
                'date_archivage' => now(), 
                'montant_du' => $dette->montant_du,
                'montant_restant' => $dette->montant_restant
            ];
            $this->mongoDette->insert($detteData);
            $dette->delete();
        }
        Log::info('Archivage MongoDB des dettes terminées.');
    }

    public function restoreDebt($id)
{
    $archivedDebt = $this->getArchivedDebtById((int)$id);

    // Si c'est une collection, il faut récupérer le premier élément
    if ($archivedDebt instanceof \Illuminate\Support\Collection) {
        $archivedDebt = $archivedDebt->first();
    }
    if ($archivedDebt) {
        $restoredDebt = Dette::create([
            'client_id' => $archivedDebt['client_id'],  // Utilisation de l'array syntaxe si nécessaire
            'montant' => $archivedDebt['montant'],
            'articles' => $archivedDebt['articles'],
            'paiements' => $archivedDebt['paiements'],
            'montant_du' => $archivedDebt['montant_du'],
            'montant_restant' => $archivedDebt['montant_restant'],
        ]);

        // Suppression de la dette archivée
        $archivedDebt->delete();

        Log::info('Dette restaurée avec succès depuis MongoDB avec l\'ID: ' . $id);
        return $restoredDebt;
    }

    Log::error('Erreur: Dette archivée introuvable avec l\'ID: ' . $id);
    return null;
}


    public function getAllArchivedDebts($filter = [])
    {
        return MongoDette::where($filter)->get()->toArray();
    }

    public function getArchivedDebtsByClientId($clientId)
    {
        return MongoDette::where('client_id', (int) $clientId)->get()->toArray();
    }

    public function getArchivedDebtById($id){
        $debts= MongoDette::where('id','=',$id)->get();
        return $debts;
    }
    

    public function restoreDebtsByDate($date)
    {
        $debts = MongoDette::where('date_archivage', $date->startOfDay())->get();
        foreach ($debts as $archivedDebt) {
            Dette::create([
                'client_id' => $archivedDebt->client_id,
                'montant' => $archivedDebt->montant,
                'articles' => $archivedDebt->articles,
                'paiements' => $archivedDebt->paiements,
                'montant_du' => $archivedDebt->montant_du,
                'montant_restant' => $archivedDebt->montant_restant,
            ]);
            $archivedDebt->delete();
        }
        return 'Restoration terminée.';
    }
}

            // Delete the archived debt from MongoDB

        // Return a success message
