<?php

namespace App\Services;

use App\Jobs\UploadClientPhotoJob;
use App\Repository\ClientRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Services\UploadService;
use App\Facades\UploadFacade;
use App\Facades\QrCodeFacade;
use App\Services\QrCodeService;
use App\Facades\ClientServiceFacade;
use App\Models\Client;
use App\Mail\LoyaltyCardMail;
use App\Exceptions\ClientCreationException;
use App\Exceptions\UserCreationException;
use App\Events\ClientCreated;
use Illuminate\Support\Facades\Event;

// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\User;
use App\Models\LoyaltyCard;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ClientServiceImpl implements ClientServiceInterface
{
    protected $clientRepository;
    protected $qrCodeService;

    public function __construct(ClientRepositoryInterface $clientRepository, QrCodeService $qrCodeService)
    {
        $this->clientRepository = $clientRepository;
        $this->qrCodeService = $qrCodeService;
    }

    public function getAllClients(array $filters = []): Collection 
    {
        // Collecter les filtres et passer à la méthode du repository
        $filters = [
            'compte' => $filters['compte']?? null,
            'active' => $filters['active']?? null,
        ];
        return $this->clientRepository->all($filters);
    }

    public function createClient(array $data): Client
    {
        // Traitement de la photo localement pour pouvoir l'utiliser dans l'événement
        $photo = null;
        if (isset($data['user']['photo']) && $data['user']['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $photo = $data['user']['photo'];
        }
        $photoBase64 = $photo ? UploadFacade::getImageAsBase64($photo) : null;
        $qrCodeBase64 = QrCodeFacade::generateBase64QrCode($data['telephone']);
        // Générer la carte de fidélité avec QR code
        $this->qrCodeService->createLoyaltyCard(
            $data['surname'],
            $data['telephone'],
            $photoBase64,
            $qrCodeBase64
        );
        $data['qrcode'] = $qrCodeBase64;
        // Préparer les données utilisateur
        $userData =$data['user'];
        $userData = empty($userData) ? null : collect($userData)->except(['password_confirmation'])->toArray();
        try {
            $client = $this->clientRepository->create($data, $userData);
            if ($photo) {
                try {
                    $url = Storage::disk('public')->url($photo);
                    $uploadedFileUrl = Cloudinary::upload($url)->getSecurePath();
                    $client->user->photo = $uploadedFileUrl; 
                    $client->user->save();  
                } catch (\Exception $e) {
                    // En cas d'erreur Cloudinary, stocker la photo localement dans la base de données
                    $client->user->photo = $photoBase64;
                    $client->user->save();  
                    Log::error('Erreur lors du téléchargement de la photo sur Cloudinary : ' . $e->getMessage());
                }
            }
        } catch (UserCreationException $e) {
            throw $e;
        } catch (ClientCreationException $e) {
            throw $e;
        }
        // Lever l'événement ClientCreated
        if ($photo) {
            Event::dispatch(new ClientCreated($client, $photo));
        }
        if ($client->user->role_id == 2) {
            Mail::to($client->user->login)->send(new LoyaltyCardMail($client));
        }
        return $client;
    }

  
    public function getClientById($id): ?Client
    {
        $client = $this->clientRepository->find($id);
        if ($client && $client->user && $client->user->photo) {
            $client->photo_base64 = UploadFacade::getImageAsBase64($client->user->photo);
        }
        return $client;
    }
    

    public function getClientByTelephone(string $telephone): ?Client
    {
        $client = $this->clientRepository->ByTelephone($telephone);
        if ($client && $client->user && $client->user->photo) {
            $client->photo_base64 = UploadFacade::getImageAsBase64($client->user->photo);
        } else {
            $client->photo_base64 = null;
        }
    
        return $client;
    }
    
    
}
