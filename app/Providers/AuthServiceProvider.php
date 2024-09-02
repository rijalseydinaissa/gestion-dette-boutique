<?php

namespace App\Providers;
use App\Models\Client;
use App\Models\Article;
use App\Models\User;
use App\Policies\ArticlePolicy;
use Illuminate\Support\Facades\Gate;





// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
        $this->registerPolicies();
        // Gate::define('access-client', function (User $user, Client $client) {
        //     return $user->role && $user->role->name === 'Client' && $client->user_id === $user->id;
        // });
        Gate::define('access-articles', [ArticlePolicy::class, 'access']);
        Gate::define('Boutiquier', [ArticlePolicy::class, 'isBoutiquier']);
        Gate::define('Admin', [ArticlePolicy::class, 'isAdmin']);



        //
    }
}
