<?php

namespace App\Repository;

interface DetteRepositoryInterface
{
    public function createDebt(array $data);
    public function getAllDebts($statut = null);
    public function getDebtsById($id);
    public function getArticlesByDette($id);
    // public function addDettePaeiment($id, $data);
}
