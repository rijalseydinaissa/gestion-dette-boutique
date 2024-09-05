<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'login' => $this->login,
            // 'role' => new RoleResource($this->whenLoaded('role')),
            'etat' => $this->etat,
            'photo' => $this->photo, 
        ];
    }
}
