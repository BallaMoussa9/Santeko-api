<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name,' . $this->department?->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive', // C'est déjà 'active' ou 'inactive' côté Vue maintenant
            'position' => 'nullable|string|max:255',
            'doctor_id' => 'nullable|integer|exists:doctors,id', // ✅ Cette règle est correcte
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du département est requis.',
            'name.string' => 'Le nom du département doit être une chaîne de caractères.',
            'name.max' => 'Le nom du département ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Un département avec ce nom existe déjà.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'status.required' => 'Le statut est requis.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
            'position.string' => 'La position doit être une chaîne de caractères.',
            'position.max' => 'La position ne doit pas dépasser 255 caractères.',
            'doctor_id.integer' => 'L\'ID du médecin doit être un entier.',
            'doctor_id.exists' => 'Le médecin sélectionné n\'existe pas.',
        ];
    }
  }
