<?php

namespace App\Jobs;

use App\Services\ArchiveDetteInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class ArchiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $archiveService;

    /**
     * Créer une nouvelle instance du job.
     *
     * @param ArchiveDetteInterface $archiveService
     */
    public function __construct()
    {
        // $this->archiveService = $archiveService;
    }

    /**
     * Exécuter le job.
     */
    public function handle(ArchiveDetteInterface $archiveService)
    {
        // Appeler le service pour archiver les dettes
        $archiveService->archiveSettledDebts();
         \Log::info('Archive');
    }
}
