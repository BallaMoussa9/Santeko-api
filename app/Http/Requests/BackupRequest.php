<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BackupRequest extends FormRequest
{
    /**
     * Vérifie si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        // On sécurise en s'assurant que l'utilisateur est connecté
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Règles de validation appliquées à la requête.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(['success', 'failed', 'in_progress', 'pending'])],

            'filename' => ['nullable', 'string', 'max:255'],
            'path' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'integer', 'min:0'],
            'type' => [
                'required',
                Rule::in(['database', 'files', 'full']),
            ],
            'last_run_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être l’un des suivants : success, failed, in_progress, pending.',

            'filename.string' => 'Le nom du fichier doit être une chaîne de caractères.',
            'filename.max' => 'Le nom du fichier ne peut pas dépasser 255 caractères.',

            'path.string' => 'Le chemin doit être une chaîne de caractères.',
            'path.max' => 'Le chemin ne peut pas dépasser 255 caractères.',

            'size.integer' => 'La taille doit être un nombre entier.',
            'size.min' => 'La taille ne peut pas être négative.',

            'type.required' => 'Le type de sauvegarde est obligatoire.',
            'type.in' => 'Le type doit être l’un des suivants : database, files, full.',

            'last_run_at.date' => 'La date du dernier lancement doit être une date valide.',

            'notes.string' => 'Les notes doivent être une chaîne de caractères.',
        ];
    }
}
