<?php

namespace App\Providers;

use App\Repository\DetteRepositoryImpl;
use App\Repository\ClientRepositoryInterface;
use App\Repository\ArticleRepositoryInterface;
use App\Services\ArticleServiceInterface;
use App\Services\PhotoUploadService;
use App\Services\QrCodeMailService;
use Illuminate\Support\ServiceProvider;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryImpl;
use App\Services\UploadService;
use App\Services\QrCodeService;
use App\Services\QrCodeInterface;
use App\Services\ClientServiceImpl;
use App\Repository\ClientRepositoryImpl;
use App\Services\ClientServiceInterface;
use App\Services\CloudinaryService;
use App\Repository\DetteRepositoryInterface;
use App\Services\DetteServiceImpl;
use App\Services\PaiementService;
use App\Repository\PaiementRepository;
use App\Services\ArchiveDetteInterface;
use App\Services\ArchiveMongoService;
use App\Services\ArchiveFirebaseService;
use App\Services\SmsServiceInterface;
use App\Services\TwilioService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ArticleServiceInterface::class, ArticleServiceImpl::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepositoryImpl::class);

        $this->app->bind(ClientRepositoryInterface::class, ClientRepositoryImpl::class);
        $this->app->bind(ClientServiceInterface::class, ClientServiceImpl::class);
        
        $this->app->singleton('clientservice', function ($app) {
            return new ClientServiceImpl();
        });

        $this->app->singleton('uploadservice', function ($app) {
            return new PhotoUploadService();
        });

        $this->app->singleton('qrcodefacade', function ($app) {
            return new QrCodeMailService();
        });

        $this->app->singleton('cloudinaryservice', function ($app) {
            return new CloudinaryService();
        });
        $this->app->bind(QrCodeMailService::class, QrCodeMailService::class);
        $this->app->bind(DetteRepositoryInterface::class, DetteRepositoryImpl::class);

        $this->app->singleton('dette-service', function ($app) {
            return new DetteServiceImpl($app->make(DetteRepositoryInterface::class));
        });
        $this->app->singleton(PaiementRepository::class, function ($app) {
            return new PaiementRepository();
        });

        $this->app->singleton(PaiementService::class, function ($app) {
            return new PaiementService($app->make(PaiementRepository::class));
        });
        $this->app->bind(ArchiveDetteInterface::class, function ($app) {
            // Choisir le service à utiliser en fonction des paramètres ou configurations
            if (env("WALOMOYKADIOR")=="firebase") {
                return new ArchiveFirebaseService();
            } else {
                return new ArchiveMongoService();
            }
        });
        $this->app->singleton('SmsService', function ($app) {
            return new TwilioService(); // Retourne une instance de TwilioService ou ton autre service SMS
        });
        // $this->app->bind(ArchiveDetteInterface::class, ArchiveMongoService::class);
        // $this->app->bind(ArchiveDetteInterface::class, ArchiveFirebaseService::class);
        
    }
}
