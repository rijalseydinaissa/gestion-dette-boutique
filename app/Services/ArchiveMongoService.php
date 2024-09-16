<?php

namespace App\Services;

use App\Models\MongoDette;
use App\Models\Dette;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use MongoDB\Laravel\Eloquent\Casts\ObjectId;
// use MongoDB\BSON\ObjectId;
class ArchiveMongoService implements ArchiveDetteInterface
{
    protected $mongoDette;

    public function __construct()
    {
        $this->mongoDette = new MongoDette();
    }
    public function archiveSettledDebts($request=null)
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
        // Trouver la dette archivée dans MongoDB à partir de l'ID
        $archivedDebt = MongoDette::find($id);
    
        if ($archivedDebt) {
            // Restaurer la dette dans la table principale `Dette`
            $restoredDebt = Dette::create([
                'client_id' => $archivedDebt->client_id,
                'montant' => $archivedDebt->montant,
                'articles' => $archivedDebt->articles,
                'paiements' => $archivedDebt->paiements,
                'montant_du' => $archivedDebt->montant_du,
                'montant_restant' => $archivedDebt->montant_restant,
            ]);
    
            // Supprimer la dette archivée dans MongoDB après restauration
            $archivedDebt->delete();
    
            Log::info('Dette restaurée avec succès depuis MongoDB avec l\'ID: ' . $id);
            return $restoredDebt;
        }
    
        Log::error('Erreur: Dette archivée introuvable avec l\'ID: ' . $id);
        return null;
    }
    
    public function getAllArchivedDebts($filter = [])
    {
        $date = isset($filter['date']) ? Carbon::createFromFormat('Y-m-d', $filter['date'])->format('Y_m_d') : now()->format('Y_m_d');
        $collectionName = 'dettes_archives_' . $date;
        Log::info('Nom de la collection utilisée: ' . $collectionName);
        $archivedDette = new MongoDette();
        $archivedDette->setCollectionName($collectionName);
        $query = $archivedDette->newQuery();
        if (isset($filter['client_id'])) {
            $query->where('client_id', (int) $filter['client_id']); 
        }
        if (isset($filter['date'])) {
            $query->where('date_archivage', '=', Carbon::createFromFormat('Y-m-d', $filter['date'])->startOfDay());
        }
        return $query->get()->toArray();
    }
    public function getArchivedDebtsByClientId($clientId)
    {
        $archivedDette = new MongoDette();
        
        // Récupérer toutes les dettes archivées d'un client spécifique
        $archivedDebts = $archivedDette->newQuery()
            ->where('client_id', (int) $clientId) // Utiliser le client_id pour filtrer
            ->get()
            ->toArray();

        Log::info("Récupération des dettes archivées pour le client avec ID : " . $clientId);

        return $archivedDebts;
    }
    public function getArchivedDebtById($id){
        $debts= MongoDette::where('id','=',$id)->get();
        // dd($debts);
        return $debts;
    }
    
    

    // public function restoreDebtsByDate($date){
    //     $debts= MongoDette::where('date','=',$date)->get();
    //     return $debts;
    // }

    public function restoreDebtsByDate( $date)
    {
        // Formater la date pour correspondre à la collection des dettes archivées
        $formattedDate = $date->format('Y_m_d');
        $collectionName = 'dettes_archives_' . $formattedDate;

        // Récupérer les dettes archivées à cette date
        $archivedDette = new MongoDette();
        $archivedDette->setCollectionName($collectionName);

        $debts = $archivedDette->newQuery()->where('date_archivage', '=', $date->startOfDay())->get();

        if ($debts->isEmpty()) {
            return 'Aucune dette trouvée pour cette date.';
        }

        $restoredDebts = [];
        foreach ($debts as $archivedDebt) {
            // Restaurer dans la table Dette
            $restoredDebt = Dette::create([
                'client_id' => $archivedDebt->client_id,
                'montant' => $archivedDebt->montant,
                'articles' => $archivedDebt->articles,
                'paiements' => $archivedDebt->paiements,
                'montant_du' => $archivedDebt->montant_du,
                'montant_restant' => $archivedDebt->montant_restant,
            ]);
            $archivedDebt->delete();
            // Ajouter à la liste des dettes restaurées
            $restoredDebts[] = $restoredDebt;
        }

        Log::info('Restoration des dettes archivées pour la date : ' . $date->format('Y-m-d'));
        
        return $restoredDebts;
    }
}
