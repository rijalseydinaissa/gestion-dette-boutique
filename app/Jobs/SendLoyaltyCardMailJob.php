<?php

namespace App\Jobs;

use App\Facades\QrCodeFacade;
use App\Facades\QrCodeMailFacade;
use App\Mail\LoyaltyCardMail;
use App\Services\QrCodeMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Client;

class SendLoyaltyCardMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $client;
    protected $qrCodeMailService;

    /**
     * Create a new job instance.
     *
     * @param Client $client
     * @param QrCodeMailService $qrCodeMailService
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        // $this->qrCodeMailService = $qrCodeMailService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // dd('3');
            // Utiliser le service pour générer le QR code et envoyer l'e-mail
            $qrCodeMailService = app(QrCodeMailService::class);
            $qrCodeMailService->generateQrCodeAndSendMail($this->client);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du job SendLoyaltyCardMailJob : ' . $e->getMessage());
        }
    }}
