<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Demande;
use App\Models\User;
use App\Models\Dette;
use App\Notifications\EnvoyerMessage;
use App\Notifications\RelanceDemande;
use App\Notifications\OverduePaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;



class DemandeController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $client = Client::where('user_id', $user->id)->first();
            if (!$client) {
                return response()->json([
                    'status' => 'erreur',
                    'message' => 'Le client associé à cet utilisateur n\'existe pas.',
                ], 404);
            }
            $articleData = $request->input('articles');
            $montant_total = 0;
            foreach ($articleData as $article) {
                $montant_total += $article['quantite'] * $article['prix'];
            }
            $demande = Demande::create([
                'client_id' => $client->id,
                'montant' => $montant_total,
                'status' => 'en attente',
            ]);
            foreach ($articleData as $article) {
                $demande->articles()->attach($article['id'], [
                    'quantite' => $article['quantite'],
                    'prix' => $article['prix'],
                ]);
            }
            $this->notifyBoutiquiers($demande);
            return response()->json([
                'status' => 'succès',
                'data' => $demande->load('articles'),
                'message' => 'Demande de dettes créée avec succès',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erreur',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    protected function notifyBoutiquiers(Demande $demande)
    {
        $boutiquiers = User::whereHas('role', function($query) {
            $query->where('name', 'Boutiquier');
        })->get();
        foreach ($boutiquiers as $boutiquier) {
            $client = $boutiquier->client; // Utiliser la relation définie dans User
            if ($client && $client->telephone) {
                // Créer un message personnalisé
                $customMessage = "le client {$client->surname}, a fait une demande de dette {$demande->montant_total}.";
                    $boutiquier->notify(new EnvoyerMessage( $client->telephone, $customMessage));
            }
        }
    }
     // Nouvelle méthode pour lister toutes les demandes
     public function index()
     {
         try {
             $demandes = Demande::with('articles')->get();
             return response()->json([
                 'status' => 'succès',
                 'data' => $demandes,
             ], 200);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'erreur',
                 'message' => $e->getMessage(),
             ], 500);
         }
     }
     public function traiterDemande(Request $request, $id)
    {
        try {
            $user = auth()->user();
            if (!$user->role || $user->role->name !== 'Boutiquier') {
                return response()->json([
                    'status' => 'erreur',
                    'message' => 'Seuls les boutiquiers peuvent traiter les demandes.',
                ], 403);
            }
            $demande = Demande::findOrFail($id);
            $request->validate([
                'status' => 'required|in:accept,refus',
            ]);
            $demande->status = $request->status;
            $demande->save();
            if ($request->status === 'accept') {
                $dette = new Dette([
                    'client_id' => $demande->client_id,
                    'montant' => $demande->montant,
                    'date_echeance' => now()->addDays(20), 
                ]);
                $dette->save();
                foreach ($demande->articles as $article) {
                    $dette->articles()->attach($article->id, [
                        'qteVente' => $article->pivot->quantite,
                        'prixVente' => $article->pivot->prix,
                    ]);
                }
            }
            $this->notifierClient($demande);
            return response()->json([
                'status' => 'succès',
                'data' => $demande->load('articles'),
                'message' => 'Demande traitée avec succès',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'erreur',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
     protected function notifierClient(Demande $demande)
     {
         $client = Client::find($demande->client_id);
         if ($client && $client->telephone) {
             $status = $demande->status === 'accept' ? 'accept' : 'refus';
             $message = "Votre demande de dette d'un montant de {$demande->montant_total} a été {$status}.";
             if ($demande->commentaire) {
                 $message .= " Commentaire: {$demande->commentaire}";
             }
               $client->notify(new EnvoyerMessage( $client->telephone,  $message));
         }
     }  
     public function relanceDemande(Request $request, $id)
     {
         try {
             $demande = Demande::findOrFail($id);
             if ($demande->status !== 'refus') {
                 return response()->json([
                     'status' => 'erreur',
                     'message' => 'Seules les demandes refusées peuvent être relancées.',
                 ], 400);
             }
             $boutiquiers = User::whereHas('role', function($query) {
                 $query->where('name', 'Boutiquier');
             })->get();
             $message = "Relance pour la demande de dette n°{$demande->id} d'un montant de {$demande->montant_total} FCFA.";
             foreach ($boutiquiers as $boutiquier) {
                 if ($boutiquier->client && $boutiquier->client->telephone) {
                         $boutiquier->notify(new EnvoyerMessage( $boutiquier->client->telephone,  $message));
                 }
             }
             $demande->status = 'en_attente';
             $demande->save();
             return response()->json([
                 'status' => 'succès',
                 'message' => 'La demande a été relancée avec succès.',
                 'data' => $demande
             ], 200);
         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'erreur',
                 'message' => $e->getMessage(),
             ], 500);
         }
     }
    }