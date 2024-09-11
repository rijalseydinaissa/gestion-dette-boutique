<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Ensuite, on applique la validation spécifique au numéro de téléphone
        $pattern = '/^(\+?221)?(77|76|75|70|78|33)[0-9]{7}$/';
        
        // Vérification avec l'expression régulière
        if (!preg_match($pattern, $value)) {
            $fail('Le numéro de téléphone doit être un numéro valide. Ex: +22177-000-00-00 ou 770000000.');
        }
    }
}
