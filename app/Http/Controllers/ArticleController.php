<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Services\ArticleServiceInterface;
use App\Traits\ApiResponse;
use App\Http\Resources\ArticleResource;

use Validator;

class ArticleController extends Controller
{

    protected $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }
    public function index(Request $request)
    {
        // $this->authorize('access', Article::class);
        // Récupération du paramètre 'disponible' de la requête
        $disponible = $request->query('disponible');
        if ($disponible === 'oui') {
            $articles = Article::where('qteStock', '>', 0)->get();
        } elseif ($disponible === 'non') {
            $articles = Article::where('qteStock', '=', 0)->get();
        } else {
            $articles = Article::all();
        }
        return response()->json(ArticleResource::collection($articles));
    }
    

    public function create()
    {
        return view('articles.create');
    }

    public function store(Request $request)
    {
        $validated = Validator::make ($request->all(),[
            'libelle' => 'required',
            'prix' => 'required|numeric',
            'qteStock' => 'required|integer',
        ]);
        if ($validated->fails()) {
            return response()->json([
                'statut' => 'error',
                'data' => $validated->errors()
            ], 422);
        }
        $article = Article::create([
            'libelle' => $request->libelle,
            'prix' => $request->prix,
            'qteStock' => $request->qteStock,
        ]);

        return response()->json([
            // 'status' => 'success',
            'data' => new ArticleResource($article)
        ], 201);
    }

    public function show($id)
    {
        try {
            //code...
            $article = Article::findOrFail($id);
            return response()->json(new ArticleResource($article));
        } catch (\Throwable $th) {
            return response()->json([
               'status' => 'error',
               'message' => 'Article introuvable.',
            ], 404);
        }
        
    }

   

    public function update(Request $request)
    {
        $articles = $request->input('articles', []);
        if (empty($articles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Le tableau articles doit contenir au moins un article pour la mise à jour.',
            ], 422);
        }
        $failedUpdates = [];
        $successfulUpdates = [];


        // Parcourir chaque article dans la requête
        foreach ($articles as $articleData) {
            $validator = Validator::make($articleData, [
                'id' => 'required|exists:articles,id',
                'qteStock' => 'required|integer|min:1',
            ]);
            if ($validator->fails()) {
                // Ajouter l'ID de l'article avec l'erreur de validation dans failedUpdates
                $failedUpdates[] = [
                    'id' => $articleData['id'],
                    'error' => $validator->errors()->first(),
                ];
            } else {
                // Mettre à jour la quantité de l'article si la validation réussit
                $article = Article::findOrFail($articleData['id']);
                // dd($articleData["id"]);
                if ($article) {
                    $article->qteStock += $articleData['qteStock'];
                    $article->save();
                    $successfulUpdates[] = new ArticleResource($article);
                }
            }
        }
    
        return response()->json([
            'status' => 'success',
            'updated_articles' => $successfulUpdates,
            'failed_updates' => $failedUpdates,
        ], 200);
    }
    

    public function updateStock(Request $request, $id)
    {
        // Validation de la nouvelle quantité en stock
        $validated = Validator::make($request->all(), [
            'qteStock' => 'required|integer|min:0',
        ]);
        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => $validated->errors()
            ], 422);
        }
        $article = Article::find($id);
        if (!$article) {
            return response()->json([
                'status' => 'error',
                'message' => 'Article not found',
            ], 404);
        }
        $article->qteStock += $request->input('qteStock');
        $article->save();
        return response()->json([
            'status' => 'success',
            'data' => new ArticleResource($article)
        ]);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return  response()->json(['message' => 'Article deleted successfully']);
    }
    public function searchByLibelle(Request $request)
{
    // Validation du libellé
    $validated = Validator::make($request->all(), [
        'libelle' => 'required|string',
    ]);

    if ($validated->fails()) {
        return response()->json([
            'status' => 'error',
            'data' => $validated->errors()
        ], 422);
    }

    // Recherche de l'article par son libellé
    $article = Article::where('libelle', $request->input('libelle'))->first();

    if (!$article) {
        return response()->json([
            'status' => 'error',
            'message' => 'Article not found',
        ], 404);
    }

    // Retour de la réponse avec l'article trouvé
    return response()->json(new ArticleResource($article));
}

}

