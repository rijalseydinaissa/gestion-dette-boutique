<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Dette;

use App\Observers\ClientObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
 
#[ObservedBy([ClientObserver::class])]

class Client extends Model
{
    use HasFactory;
   // public mixed $user_id;
    protected $fillable = [
        'surname',
        'adresse',
        'telephone',
        'qrcode',
        'user_id'
    ];
    protected $hidden = [
        //  'password',
        'created_at',
        'updated_at',
    ];
    protected $temporaryAttributes = [];

    // Méthode pour définir un attribut temporaire
    public function setTemporaryAttribute($key, $value)
    {
        $this->temporaryAttributes[$key] = $value;
    }

    // Méthode pour obtenir un attribut temporaire
    public function getTemporaryAttribute($key)
    {
        return $this->temporaryAttributes[$key] ?? null;
    }
    protected $appends = ['photo_base64'];

    function user() {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }
}
