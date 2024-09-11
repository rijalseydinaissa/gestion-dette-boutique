<?php
namespace App\Services;

use App\Exceptions\ServiceException;
use App\Repository\PaiementRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PaiementService
{
    protected $paiementRepository;

    public function __construct(PaiementRepository $paiementRepository)
    {
        $this->paiementRepository = $paiementRepository;
    }

    /**
     * Ajouter un paiement à une dette.
     *
     * @param Request $request
     * @param int $detteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPayment(Request $request, $detteId)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:0.01',
        ]);
        try {
            // Appeler le service pour ajouter le paiement
            $paiement = $this->paiementRepository->createPaiement(['dette_id' => $detteId, 'montant' => $validatedData['montant']]);
            return response()->json([
                'statut' => 'success',
                'message' => 'Paiement ajouté avec succès.',
                'data' => $paiement,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'statut' => 'echec',
                'message' => 'Erreur lors de l\'ajout du paiement: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
