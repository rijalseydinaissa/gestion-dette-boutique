<?php

namespace App\Services;

interface DetteServiceInterface
{
    public function addDebt(array $data);
    public function getAllDettes();
    public function getDebtsById($id);
    public function getArticlesByDette($id);
    public function getPaiementsByDette($id);
    // public function addPayment($detteId, $montant);
}
