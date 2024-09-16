<?php

// app/Http/Controllers/DetteController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\DetteFacade;
use App\Services\DetteServiceImpl;
use App\Services\ArchiveDetteInterface ;
use App\Http\Requests\StoreDetteRequest;

class DetteController extends Controller
{
    protected $debtService;
    protected $archiveService;


    public function __construct(DetteServiceImpl $debtService, ArchiveDetteInterface $archiveService)
    {
        $this->debtService = $debtService;
        $this->archiveService = $archiveService;
    }
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'montant' => 'required|numeric|min:25',
        'client_id' => 'required|exists:clients,id',
        'articles' => 'required|array|min:1', // Vérification si articles est un tableau
        'articles.*.articleId' => 'required|exists:articles,id',
        'articles.*.qteVente' => 'required|integer|min:1',
        'articles.*.prixVente' => 'required|numeric|min:25',
        'paiement.montant' => 'nullable|numeric|min:0',
        'date_echeance' => 'required|date|after_or_equal:today',
    ]);
    

    try {
        $dette = $this->debtService->addDebt($validatedData);

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
    // public function articlesByDette($id){
    //     $articles= DetteFacade::getArticlesByDette($id);
    //     return response()->json($articles, 200);
    // }

    public function articlesByDette($id)
    {
        try {
            $articles = $this->debtService->getArticlesByDette($id);
            return response()->json([
                'message' => 'Articles trouvés',
                'data' => $articles
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function paiementsByDette($id)
    {
        try {
            $paiements = $this->debtService->getPaiementsByDette($id);
            return response()->json([
                'message' => 'Paiements trouvés',
                'data' => $paiements
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function showArchived(Request $request)
    {
        $filter = [
            'client_id' => $request->query('client_id'),
            'date' => $request->query('date'),
        ];
        $archivedDebts = $this->archiveService->getAllArchivedDebts(array_filter($filter));
        return response()->json($archivedDebts);
    }
   

}
