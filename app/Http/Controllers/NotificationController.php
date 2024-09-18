<?php

namespace App\Http\Controllers;

use App\Notifications\EnvoyerMessage;
use App\Services\TwilioService;
use Auth;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;
use Log;

class NotificationController extends Controller
{
    protected $notificationManager;

    public function __construct(TwilioService $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    // public function sendReminderToClient($id)
    // {
    //     $client = Client::findOrFail($id);
    //     $montantRestant = $client->dettes->sum('montant_restant');
    //     $message = "Vous avez une dette de {$montantRestant} à payer.";
    
    //     if ($this->notificationManager->send($client->telephone, $message)) {
    //         return response()->json(['success' => 'Notification envoyée'], 200);
    //     }
    //     return response()->json(['error' => 'Échec de l\'envoi'], 500);
    // }
    

    // public function sendReminderToAllClients()
    // {
    //     $clients = Client::has('dettes')->get();
    
    //     foreach ($clients as $client) {
    //         $montantRestant = $client->dettes->sum('montant_restant');
    //         $message = "Vous avez une dette de {$montantRestant} à payer.";
    
    //         if ($this->notificationManager->sendNotification($client->telephone, $message)) {
    //             // Enregistrer la notification
    //             Notification::create([
    //                 'client_id' => $client->id,
    //                 'message' => $message,
    //             ]);
    //         }
    //     }
    
    //     return response()->json(['success' => 'Notifications envoyées à tous les clients'], 200);
    // }
    // public function sendCustomMessage(Request $request)
    // {
    //     $clients = Client::has('dettes')->get();
    //     $customMessage = $request->input('message');

    //     foreach ($clients as $client) {
    //         if ($this->notificationManager->sendNotification($client->telephone, $customMessage)) {
    //             // Enregistrer la notification
    //             Notification::create([
    //                 'client_id' => $client->id,
    //                 'message' => $customMessage,
    //             ]);
    //         }
    //     }
    //     return response()->json(['success' => 'Messages envoyés à tous les clients'], 200);
    // }

    // public function getUnreadNotifications(Client $client)
    //     {
    //         $notifications = $client->notifications()->where('is_read', false)->get();

    //         return response()->json([
    //             'status' => 'success',
    //             'notifications' => $notifications,
    //         ]);
    //     }
    // // Récupérer les notifications lues
    // public function getReadNotifications(Client $client)
    // {
    //     $notifications = $client->notifications()->where('is_read', true)->get();
    //     return response()->json([
    //         'status' => 'success',
    //         'notifications' => $notifications,
    //     ]);
    // }
    // public function markAsRead(Notification $notification)
    // {
    //     $notification->is_read = true;
    //     $notification->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Notification marked as read',
    //     ]);
    // }

    public function sendReminderToClient($id)
    {
        $client = Client::find($id);
        if (!$client) {
            return response()->json(['error' => 'Client introuvable'], 404);
        }
        $user = $client->user;
        if (!$user) {
            return response()->json(['error' => 'Utilisateur associé au client introuvable'], 404);
        }
        $montantRestant = $client->dettes->sum('montant_restant');
        $message = "Vous avez une dette de {$montantRestant} à payer.";
        $user->notify(new EnvoyerMessage($client->telephone, $message));
        return response()->json(['success' => 'Notification envoyée'], 200);
    }
    public function sendReminderToAllClients()
{
    // Récupérer tous les clients avec des dettes impayées
    $clients = Client::whereHas('dettes', function ($query) {
        $query->whereRaw('montant > (SELECT COALESCE(SUM(paiements.montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
    })->get();

    $resultats = [];

    foreach ($clients as $client) {
        // Récupérer l'utilisateur associé au client
        $user = $client->user;

        if ($user) {
            // Calculer le montant total dû
            $montantTotal = $client->dettes()
                ->selectRaw('SUM(montant - COALESCE((SELECT SUM(montant) FROM paiements WHERE dette_id = dettes.id), 0)) as total_du')
                ->value('total_du');

            $message = "Vous avez une dette de {$montantTotal} à payer.";

            try {
                // Envoyer la notification via l'utilisateur associé
                $user->notify(new EnvoyerMessage($client->telephone, $message));

                // Ajouter un statut de succès pour ce client
                $resultats[] = [
                    'montant_du' => $montantTotal,
                ];
            } catch (\Exception $e) {
                // Gérer les erreurs d'envoi de notification
                $resultats[] = [
                    'montant_du' => $montantTotal,
                ];
            }
        } else {
            // Si aucun utilisateur n'est associé au client
            $resultats[] = [
                'statut' => 'échec',
                'erreur' => 'Aucun utilisateur associé au client',
                'client_id' => $client->id,
                'nom' => $client->surname,
                'telephone' => $client->telephone,
            ];
        }
    }

    return response()->json([
        'message' => 'Notifications traitées',
        'resultats' => $resultats
    ]);
}

public function sendReminderToSelectedClients(Request $request)
{
    $request->validate([
        'message_template' => 'required|string',
        'client_ids' => 'required|array',
        'client_ids.*' => 'integer|exists:clients,id'
    ]);

    $clients = Client::whereIn('id', $request->client_ids)
        ->whereHas('dettes', function ($query) {
            $query->whereRaw('montant > (SELECT COALESCE(SUM(paiements.montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
        })->get();

    $resultats = [];

    foreach ($clients as $client) {
        $montantTotal = $client->dettes()
            ->selectRaw('SUM(montant - COALESCE((SELECT SUM(montant) FROM paiements WHERE dette_id = dettes.id), 0)) as total_du')
            ->value('total_du');

        $message = str_replace(
            ['{nom}', '{montant}'],
            [$client->surname, number_format($montantTotal, 2)],
            $request->message_template
        );

        try {
            // Envoyer le SMS
            $client->notify(new EnvoyerMessage($client->telephone, $message));

            $resultats[] = [
                'client_id' => $client->id,
                'nom' => $client->surname,
                'telephone' => $client->telephone,
                'montant_du' => $montantTotal,
                'statut' => 'envoyé'
            ];
        } catch (\Exception $e) {
            $resultats[] = [
                'client_id' => $client->id,
                'nom' => $client->surname,
                'telephone' => $client->telephone,
                'montant_du' => $montantTotal,
                'statut' => 'échec',
                'erreur' => $e->getMessage()
            ];
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Notifications envoyées',
        'resultats' => $resultats
    ]);
}
public function getUnreadNotifications()
{
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.'
            ], 401);
        }
        Log::info('Utilisateur authentifié', ['user' => $user]);
        $client = Client::where('user_id', $user->id)->first();
        Log::info('Client associé', ['client' => $client]);
        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun client associé à cet utilisateur.'
            ], 404);
        }
        $unreadNotifications = Notification::where('notifiable_type', 'App\Models\Client')
            ->where('notifiable_id', $client->id)
            ->whereNull('read_at')
            ->get();
        return response()->json([
            'success' => true,
            'notifications' => $unreadNotifications,
            'message' => 'Notifications non lues récupérées avec succès.'
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la récupération des notifications non lues', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la récupération des notifications non lues.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
