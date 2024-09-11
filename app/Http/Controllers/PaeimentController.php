<?php

// app/Http/Controllers/DebtController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaiementService;

class PaeimentController extends Controller
{
    protected $paymentService;

    public function __construct(PaiementService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function addPayment(Request $request, $detteId)
    {
        // dd($detteId);
        try {
            // Validation des données
            $validatedData = $request->validate([
                'montant' => 'required|numeric|min:1'
            ]);

            // Ajouter le paiement via le service
            $paiement = $this->paymentService->addPayment($request, $detteId);

            return response()->json([
                'statut' => 'success',
                'message' => 'Paiement ajouté avec succès.',
                'data' => $paiement
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'statut' => 'echec',
                'message' => 'Erreur lors de l\'ajout du paiement: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
