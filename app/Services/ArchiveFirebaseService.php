<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use App\Services\ArchiveDetteInterface;

class ArchiveFirebaseService implements ArchiveDetteInterface
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount('/home/issa/Bureau/Laravel/GestionShop/gestion-dette-boutique/walonayneikh-firebase-adminsdk-yt3vc-1f7ac1a455.json')
            ->withDatabaseUri('https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-yt3vc%40walonayneikh.iam.gserviceaccount.com');

        $this->database = $factory->createDatabase();
    }

    public function archiveSettledDebts($request = null)
    {
        $newData = $this->database->getReference(date('Y-m-d H:i:s'))->push($request);
        return $newData->getValue();
    }

    public function restoreDebt($id)
    {
        $debtRef = $this->database->getReference('dettes/' . $id);
        $debt = $debtRef->getValue();
        if ($debt) {
            $debtRef->remove();
            return $debt;
        }
        return null;
    }

    public function getAllArchivedDebts($filter = [])
    {
        return $this->database->getReference('dettes')->getValue();
    }

    public function getArchivedDebtsByClientId($clientId)
    {
        $archivedDebts = $this->database->getReference('dettes')->getValue();
        $filteredDebts = array_filter($archivedDebts, fn($debt) => $debt['client_id'] === (int) $clientId);
        return $filteredDebts;
    }

    public function getArchivedDebtById($id)
    {
        return $this->database->getReference('dettes/' . $id)->getValue();
    }

    public function restoreDebtsByDate($date)
    {
        // Logique pour restaurer des dettes à partir de Firebase par date
    }
}
