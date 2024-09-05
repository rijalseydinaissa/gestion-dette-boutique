<?php

namespace App\Services;

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

// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\User;
use App\Models\LoyaltyCard;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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
        // Traitement de la photo
        if (isset($data['user']['photo']) && $data['user']['photo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['user']['photo'] = UploadFacade::uploadImage($data['user']['photo']);
        }
        $photoBase64 = $data['user']['photo'] ? UploadFacade::getImageAsBase64($data['user']['photo']) : null;
        $qrCodeBase64 = QrCodeFacade::generateBase64QrCode($data['telephone']);

        $this->qrCodeService->createLoyaltyCard(
            $data['surname'],
            $data['telephone'],  
            $photoBase64,
            $qrCodeBase64
        );
        $data['qrcode'] = $qrCodeBase64;

        $userData = isset($data['user']) ? [
            'nom' => $data['user']['nom'],
            'prenom' => $data['user']['prenom'],
            'login' => $data['user']['login'],
            'password' => bcrypt($data['user']['password']),
            'etat' => $data['user']['etat'],
            'role_id' => $data['user']['role'],
            'photo' => $photoBase64,
        ] : null;

        try {
            $client = $this->clientRepository->create($data, $userData);
        } catch (UserCreationException $e) {
            throw $e;
        } catch (ClientCreationException $e) {
            throw $e;
        }

        Mail::to($client->user->login)->send(new LoyaltyCardMail($client));

        return $client;
    }
    // public function createClient(array $data): Client
    // {
    //     // Traitement de la photo
    //     if (isset($data['user']['photo']) && $data['user']['photo'] instanceof \Illuminate\Http\UploadedFile) {
    //         $uploadedFileUrl = Cloudinary::upload($data['user']['photo']->getRealPath())->getSecurePath();
    //         $data['user']['photo'] = $uploadedFileUrl;
    //     }

    //     $photoBase64 = $data['user']['photo'] ? UploadFacade::getImageAsBase64($data['user']['photo']) : null;
    //     $qrCodeBase64 = QrCodeFacade::generateBase64QrCode($data['telephone']);

    //     $this->qrCodeService->createLoyaltyCard(
    //         $data['surname'],
    //         $data['telephone'],  
    //         $photoBase64,
    //         $qrCodeBase64
    //     );

    //     $data['qrcode'] = $qrCodeBase64;

    //     $userData = isset($data['user']) ? [
    //         'nom' => $data['user']['nom'],
    //         'prenom' => $data['user']['prenom'],
    //         'login' => $data['user']['login'],
    //         'password' => bcrypt($data['user']['password']),
    //         'etat' => $data['user']['etat'],
    //         'role_id' => $data['user']['role'],
    //         'photo' => $data['user']['photo'],  // On garde l'URL de la photo sur Cloudinary
    //     ] : null;

    //     $client = $this->clientRepository->create($data, $userData);
    //     Mail::to($client['user']['login'])->send(new LoyaltyCardMail($client));

    //     return $client;
    // }
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
