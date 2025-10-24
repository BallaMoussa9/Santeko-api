<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
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
        'admin_id' => ['nullable', 'integer', 'exists:admins,id'],
        'recever_id' => ['nullable', 'integer', 'exists:users,id'],
        'title' => ['nullable', 'string', 'max:255'],
        'content' => ['nullable', 'string'],
        'status' => ['nullable', Rule::in(['sent', 'delivered', 'read'])],
        'priority' => ['nullable', 'string', 'max:255'],
        'start_time' => ['nullable', 'date'],
        'end_time' => ['nullable', 'date'],
    ];
}
public function messages(): array
{
    return [
        'admin_id.integer' => 'L\'admin_id doit être un entier.',
        'admin_id.exists' => 'L\'admin spécifié est introuvable.',

        'recever_id.integer' => 'Le recever_id doit être un entier.',
        'recever_id.exists' => 'Le receveur spécifié est introuvable.',

        'title.string' => 'Le titre doit être une chaîne de caractères.',
        'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',

        'content.string' => 'Le contenu doit être une chaîne de caractères.',

        'status.in' => 'Le statut doit être l\'une des valeurs suivantes : sent, delivered ou read.',

        'priority.string' => 'La priorité doit être une chaîne de caractères.',
        'priority.max' => 'La priorité ne doit pas dépasser 255 caractères.',

        'start_time.date' => 'La date de début doit être une date valide.',
        'end_time.date' => 'La date de fin doit être une date valide.',
    ];
}


}
