<?php
namespace App\Services;

use App\Repository\ArticleRepositoryInterface;

class ArticleServiceImpl implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function all()
    {
        return $this->articleRepository->all();
    }

    public function create(array $data)
    {
        return $this->articleRepository->create($data);
    }

    public function find($id)
    {
        return $this->articleRepository->find($id);
    }

    public function update($id, array $data)
    {
        return $this->articleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->articleRepository->delete($id);
    }

    public function findByLibelle($libelle)
    {
        return $this->articleRepository->findByLibelle($libelle);
    }

    public function findByEtat($etat)
    {
        return $this->articleRepository->findByEtat($etat);
    }
}