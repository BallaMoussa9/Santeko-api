<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistereUpdateRequest extends FormRequest
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
            //'language_id' => ['required', 'integer', 'exists:languages,id'],
          //  'role_id' => ['required', 'integer', 'exists:roles,id'],
           // 'department_id' => ['required', 'integer', 'exists:departments,id'],
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required| string| max:255',
            'birth_date'    => 'required|date|before:today',
            'phone'         => 'required| string| max:15',
            'country'       => 'required| string| max:255',
            'city'          => 'required| string| max:255',
            'address'       => 'required| string| max:255',
            'profile_photo' => 'nullable| image| mimes:jpeg,png,jpg,gif,svg| max:2048',
            'email'         => 'required| email|max:255|unique:users,email',
            'password'      => 'required| string| min:8|confirmed',
        ];
    }
    public function messages(): array
{
    return [
        'first_name.required' => 'Le prénom est obligatoire.',
        'first_name.string'   => 'Le prénom doit être une chaîne de caractères.',
        'first_name.max'      => 'Le prénom ne peut pas dépasser 255 caractères.',

        'last_name.required' => 'Le nom est obligatoire.',
        'last_name.string'   => 'Le nom doit être une chaîne de caractères.',
        'last_name.max'      => 'Le nom ne peut pas dépasser 255 caractères.',

        'birth_date.required' => 'La date de naissance est obligatoire.',
        'birth_date.date'     => 'La date de naissance doit être une date valide.',
        'birth_date.before'   => 'La date de naissance doit être antérieure à aujourd\'hui.',

        'country.required' => 'Le pays est obligatoire.',
        'country.string'   => 'Le pays doit être une chaîne de caractères.',
        'country.max'      => 'Le pays ne peut pas dépasser 255 caractères.',

        'city.required' => 'La ville est obligatoire.',
        'city.string'   => 'La ville doit être une chaîne de caractères.',
        'city.max'      => 'La ville ne peut pas dépasser 255 caractères.',

        'address.required' => 'L\'adresse est obligatoire.',
        'address.string'   => 'L\'adresse doit être une chaîne de caractères.',
        'address.max'      => 'L\'adresse ne peut pas dépasser 255 caractères.',

        'phone.required' => 'Le numéro de téléphone est obligatoire.',
        'phone.string'   => 'Le numéro de téléphone doit être une chaîne de caractères.',
        'phone.max'      => 'Le numéro de téléphone ne peut pas dépasser 15 caractères.',

        'profile_photo.image' => 'La photo de profil doit être une image.',
        'profile_photo.mimes' => 'La photo de profil doit être de type : jpeg, png, jpg, gif, svg.',
        'profile_photo.max'   => 'La taille maximale de la photo de profil est de 2 Mo.',

        'email.required' => 'L\'adresse email est obligatoire.',
        'email.email'    => 'L\'adresse email doit être valide.',
        'email.max'      => 'L\'adresse email ne peut pas dépasser 255 caractères.',
        'email.unique'   => 'Cette adresse email est déjà utilisée.',

        'password.required'  => 'Le mot de passe est obligatoire.',
        'password.string'    => 'Le mot de passe doit être une chaîne de caractères.',
        'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
    ];
}

}
