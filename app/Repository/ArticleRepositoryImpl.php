<?php
namespace App\Repository;

use App\Models\Article;
use App\Repository\ArticleRepositoryInterface;

class ArticleRepositoryImpl implements ArticleRepositoryInterface
{
    public function all($filters)
    {
        return Article::all();
    }

    public function create(array $data)
    {
        return Article::create($data);
    }

    public function find($id)
    {
        return Article::findOrFail($id);
    }

    public function update($id, array $data)
    {
        $article = $this->find($id);
        $article->update($data);
        return $article;
    }

    public function delete($id)
    {
        $article = $this->find($id);
        return $article->delete();
    }

    public function findByLibelle($libelle)
    {
        return Article::where('libelle', $libelle)->get();
    }

    public function findByEtat($etat)
    {
        return Article::where('etat', $etat)->get();
    }
}
