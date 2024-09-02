<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {
    // //    return $user->hasRole('Boutiquier');
    //      return $user->role_id == 2;
    // }

    public function access(User $user,Article $article): Response{
        return $user->role && $user->role->name === 'Boutiquier'
            ? Response::allow()
            : Response::deny('Vous devez être un Boutiquier pour accéder à cette ressource.');
    }
    public function isBoutiquier(User $user, )
    {
        // dd($user->role->name);
        return $user->role->name == 'Boutiquier';
    }

    public function isAdmin(User $user){
        return $user->role->name == 'Admin';
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article): bool
    {
        //
    }
}
