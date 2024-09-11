<?php

namespace App\Jobs;

use App\Services\ArchiveServiceInterface;
use App\Models\Dette;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Services\ArchiveDetteInterface;

class ArchiveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $dette;

    /**
     * Create a new job instance.
     *
     * @param Dette $dette
     */
    public function __construct(Dette $dette)
    {
        $this->dette = $dette;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ArchiveDetteInterface $archiveService)
    {
        $archiveService->archiveSettledDebts();
        Log::info('Job d\'archivage exécuté.');
    }
}
