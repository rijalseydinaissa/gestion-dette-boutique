<?php

namespace App\Providers;

use App\Repository\ClientRepositoryInterface;
use App\Repository\ArticleRepositoryInterface;
use App\Services\ArticleServiceInterface;
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
            return new UploadService();
        });

        $this->app->singleton('qrcodefacade', function ($app) {
            return new QrCodeService();
        });
    }
}
