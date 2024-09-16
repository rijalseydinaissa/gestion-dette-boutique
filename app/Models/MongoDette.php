<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use MongoDB\Client as MongoClient;
use MongoDB\Laravel\Eloquent\Model;

class MongoDette extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $collection;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Collection nommée par la date (si tu souhaites changer tous les jours)
        $this->collection = 'dettes_archives_2024_09_12';
        ;
    }
    // Vous pouvez aussi essayer 'object' si nécessaire

    public function setCollectionName($name)
    {
        $this->collection = $name;
    }

    // Définir les attributs modifiables en masse
    protected $fillable = [
        'client_id',
        'montant',
        'dette_id',
        'articles',
        'paiements',
        'date_archivage',
        'montant_du',
        'montant_restant',
    ];
}   