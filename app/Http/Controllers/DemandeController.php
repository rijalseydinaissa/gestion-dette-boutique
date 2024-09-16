<?php

namespace App\Http\Controllers;

use App\Jobs\SendNotificationToBoutiquierJob;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Dette;
use App\Jobs\SendNotificationToBoutiquier;
use Illuminate\Support\Facades\Auth;

class DemandeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'articles' => 'required|array',
        ]);

        $client = Auth::user()->client;

        // Validation selon la catégorie du client
        $categorie = $client->categorie_id;
        $montantTotal = $client->dettes->sum('montant_restant');

        if ($categorie == 3) { // Bronze
            if ($montantTotal > 0) {
                return response()->json(['error' => 'Les clients Bronze ne peuvent pas faire de demande avec des dettes existantes.'], 400);
            }
        } elseif ($categorie == 2) { // Silver
            if ($client->max_montant < $request->input('montant')) {
                return response()->json(['error' => 'Le montant de la demande dépasse le montant maximum autorisé pour les clients Silver.'], 400);
            }
        }

        // Création de la demande
        $demande = $client->demandes()->create([
            'montant' => $request->input('montant'),
        ]);

        // Envoi de la notification
        SendNotificationToBoutiquierJob::dispatch($demande);

        return response()->json($demande, 201);
    }
}

