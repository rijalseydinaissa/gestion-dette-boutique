<?php

// app/Http/Controllers/DetteController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\DetteFacade;
use App\Services\DetteServiceImpl;
use App\Http\Requests\StoreDetteRequest;

class DetteController extends Controller
{
    protected $debtService;

    public function __construct(DetteServiceImpl $debtService)
    {
        $this->debtService = $debtService;
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric|min:25',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:25',
            'paiement.montant' => 'nullable|numeric|min:0'
        ]);
        try {
            $dette = $this->debtService->addDebt($validatedData);

            // Retourner une réponse différente selon que la dette a été supprimée ou non
            if (is_string($dette)) {
                return response()->json(['message' => $dette], 200);
            }

            return response()->json(['message' => 'Dette ajoutée avec succès', 'data' => $dette], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function index(Request $request)
{
    // Récupérer le paramètre `statut` depuis la requête
    $statut = $request->query('statut');

    // Passer le statut au service
    $debts = $this->debtService->getAllDettes($statut);

    return response()->json($debts, 200);
}

    //get debts by id
    public function show($id){
        $debt=$this->debtService->getDebtsById($id);
        return response()->json($debt, 200);
    }
    //get articles by id detb
    public function articlesByDette($id){
        $articles= DetteFacade::getArticlesByDette($id);
        return response()->json($articles, 200);
    }

    //add paiement to debts
    // public function addPaiement(Request $request, $id){
    //     $validatedData = $request->validate([
    //        'montant' => 'required|numeric|min:100'
    //     ]);
    //     try {
    //         $debt = $this->debtService->addPayment($id, $validatedData);
    //         return response()->json(['message' => 'Paiement ajouté avec succès', 'data' => $debt], 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    // }

}
