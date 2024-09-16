<?php

namespace App\Http\Controllers;

use App\Services\ArchiveDetteInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    protected $archiveService;

    public function __construct(ArchiveDetteInterface $archiveService)
    {
        $this->archiveService = $archiveService;
    }
    // Archiver les dettes soldées
   
    public function getArchivedDebtsByClientId($clientId)
    {
        $archivedDebts = $this->archiveService->getArchivedDebtsByClientId($clientId);
        return response()->json($archivedDebts, 200);
    }
    
    public function getArchivedDebtById($id)
    {
        // dd($id);
        $archivedDebt = $this->archiveService->getArchivedDebtById((int)$id);

        if (!$archivedDebt) {
            return response()->json(['message' => 'Dette archivée introuvable'], 404);
        }

        return response()->json($archivedDebt, 200);
    }
    // Afficher une dette archivée
    public function restoreDebtsByDate($date)
    {
        try {
            // Conversion de la date reçue
            $parsedDate = Carbon::createFromFormat('Y-m-d', $date);

            // Appel du service pour restaurer les dettes
            $restoredDebts = $this->archiveService->restoreDebtsByDate($parsedDate);

            return response()->json(['message' => 'Dettes restaurées avec succès', 'dettes' => $restoredDebts], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la restauration des dettes', 'error' => $e->getMessage()], 500);
        }
    }

        public function restoreDebt($id)
        {
            $restoredDebt = $this->archiveService->restoreDebt((int)$id);

            if ($restoredDebt) {
                return response()->json(['message' => 'Dette restaurée avec succès', 'dette' => $restoredDebt], 200);
            }

            return response()->json(['message' => 'Erreur: Dette archivée introuvable'], 404);
        }

    public function restoreDebtsForClient($clientId)
    {
        try {
            $restoredDebts = $this->archiveService->getArchivedDebtsByClientId($clientId);

            if (empty($restoredDebts)) {
                return response()->json([
                    'status' => 'echec',
                    'message' => "Aucune dette archivée trouvée pour le client avec l'ID : $clientId."
                ], 404);
            }

            // Restaurer chaque dette trouvée
            foreach ($restoredDebts as $archivedDebt) {
                $this->archiveService->restoreDebt($archivedDebt['id']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Dettes restaurées avec succès.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'echec',
                'message' => 'Erreur lors de la restauration des dettes.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
}

