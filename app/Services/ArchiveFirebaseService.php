<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use MongoDB\Database;
use App\Services\ArchiveDetteInterface;
use Carbon\Carbon;

class ArchiveFirebaseService implements ArchiveDetteInterface
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount('/home/issa/Bureau/Laravel/GestionShop/gestion-dette-boutique/walonayneikh-firebase-adminsdk-yt3vc-1f7ac1a455.json')
            ->withDatabaseUri('https://walonayneikh-default-rtdb.firebaseio.com');  // Replace with your Firebase Realtime Database URL

        $this->database = $factory->createDatabase();
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function archiveSettledDebts($request = null)
        {
        $newData = $this->database->getReference(date('Y-m-d H:i:s'))->push($request);
        return response()->json($newData->getValue());
    }
    public function restoreDebt($id)
    {
        try {
            $debtRef = $this->database->getReference('dettes/' . $id);
            $debt = $debtRef->getValue();

            if ($debt) {
                // Suppression après la restauration
                $debtRef->remove();
                return response()->json(['message' => 'Dette restaurée avec succès', 'dette' => $debt]);
            } else {
                return response()->json(['message' => 'Dette introuvable'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getAllArchivedDebts($filter = [])
    {
        $archivedDebts = $this->database->getReference('dettes')->getValue();
        return response()->json($archivedDebts);
    }
    public function getArchivedDebtsByClientId($clientId){
        $archivedDebts = $this->database->getReference('dettes')->getValue();
        $filteredDebts = [];
        foreach ($archivedDebts as $id => $debt) {
            if ($debt['client_id'] === (int) $clientId) {
                $filteredDebts[$id] = $debt;
            }
        }
        return response()->json($filteredDebts);
    }

    public function getArchivedDebtById($id){

    }
    public function restoreDebtsByDate($date){
        // TODO: Implement restoreDebtsByDate method.
    }
   

}