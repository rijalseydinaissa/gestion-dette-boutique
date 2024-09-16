<?php

namespace App\Services;

interface ArchiveDetteInterface
{
    public function archiveSettledDebts($request=null);
    public function getAllArchivedDebts($filter = []);
    public function getArchivedDebtsByClientId($clientId);
    // public function restoreDebt($id);
    public function getArchivedDebtById($id);
    public function restoreDebtsByDate( $date);
    public function restoreDebt($id);
    
}
