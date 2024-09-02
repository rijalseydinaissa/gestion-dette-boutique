<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::apiResource('/clients', ClientController::class)->only(['index', 'store','show']);
});

Route::prefix('v1/users')->middleware('auth:api')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');  
    Route::post('/', [UserController::class, 'store'])->name('store');
});

Route::prefix('v1/users')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register'])->middleware(['auth:api','role:Boutiquier']);
});
Route::middleware('auth:api')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('v1/articles')->middleware(['auth:api','role:Boutiquier'])->name('articles.')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('index');          // GET /v1/articles
    Route::post('/', [ArticleController::class, 'store'])->name('store');         // POST /v1/articles
    Route::get('/{id}', [ArticleController::class, 'show'])->name('show');        // GET /v1/articles/{id}
    Route::post('/', [ArticleController::class, 'update'])->name('update');    // post /v1/articles
    // Route::patch('/{id}', [ArticleController::class, 'update'])->name('updates');  // PATCH /v1/articles/{id}e
    Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('destroy'); // DELETE /v1/articles/{id}
    Route::put('/{id}/update-stock', [ArticleController::class, 'updateStock']);
    // Route pour rechercher un article par libellé
    Route::post('/search', [ArticleController::class, 'searchByLibelle']);
});

Route::prefix('v1/clients')->middleware(['auth:api','role:Boutiquier'])->name('clients.')->group(function () {
    // Routes pour les clients
    Route::post('/', [ClientController::class, 'store'])->name('store');
    Route::post('/telephone', [ClientController::class, 'getByTelephone']);
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::get('/{id}', [ClientController::class, 'show']);
    Route::get('/{id}/user', [ClientController::class, 'show']);
    Route::put('/{id}', [ClientController::class, 'update'])->name('update');
    Route::patch('/{id}', [ClientController::class, 'update'])->name('updates');
    Route::delete('/{id}', [ClientController::class, 'destroy'])->name('destroy');
});
