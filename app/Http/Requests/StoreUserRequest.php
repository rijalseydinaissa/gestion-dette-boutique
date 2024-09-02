<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Enums\UserRole;
use App\Rules\CustumPasswordRule;
use App\Rules\PasswordRules;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login',
            'role_id' => 'required|integer|exists:roles,id', // Validation de l'ID du rôle
            'etat' => 'required|boolean',
            'password' => ['confirmed', new CustumPasswordRule()],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => "Cet login est déjà utilisé.",
            'etat.required' => 'L\'état est obligatoire.',
            'etat.boolean' => 'L\'état doit être un booléen valide.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'role_id.required' => 'Le rôle est obligatoire.',
            'role_id.integer' => 'Le rôle doit être un identifiant numérique valide.',
            'role_id.exists' => 'Le rôle spécifié n\'existe pas.',
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }
}
