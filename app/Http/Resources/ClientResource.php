<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'surname' => $this->surname,
            'telephone' => $this->telephone,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
// return [
//     'id' => $this->id,
//     'nom' => $this->nom,
//     'prenom' => $this->prenom,
//     'telephone' => $this->telephone,
//     'photo' => $this->photo_base64, // Assurez-vous que ce champ est bien présent
//     // Ajouter d'autres attributs si nécessaire
// ];