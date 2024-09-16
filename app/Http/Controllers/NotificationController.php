<?php

namespace App\Http\Controllers;

use App\Notifications\EnvoyerMessage;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Notification;
use App\Models\User;

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

    public function test(){
        $user = User::find(7);

        $user->notify(new EnvoyerMessage('+221778170068','hello world'));
        return "Notification sent";
    }

}
