<?php

namespace App\Services;

use Kreait\Firebase\Factory as FirebaseFactory;
use App\Models\Dette;
use Illuminate\Support\Facades\Log;

class ArchiveFirebaseService implements ArchiveDetteInterface
{
    public function archiveSettledDebts()
    {
        $settledDebts = Dette::where('montant', '=', 0)->get();

        $firebase = (new FirebaseFactory)->withServiceAccount(config('firebase.credentials.key_file'))
                                         ->createDatabase();
        $database = $firebase->getDatabase();
        $reference = $database->getReference('dettes_archives');

        foreach ($settledDebts as $dette) {
            $detteData = [
                'client_id' => $dette->client_id,
                'montant' => $dette->montant,
                'articles' => $dette->articles->toArray(),
                'date' => now()
            ];

            $reference->push($detteData);
            $dette->delete();
        }

        Log::info('Archivage Firebase terminé.');
    }
}
