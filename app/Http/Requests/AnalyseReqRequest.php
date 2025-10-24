<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // N'oubliez pas ceci si vous utilisez Rule::in()

class AnalyseReqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Envisagez une logique d'autorisation plus robuste ici.
        // Par exemple, vérifier si l'utilisateur est un technicien de laboratoire.
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
            // 'analyse_id' n'est pas attendu dans la requête car il vient de l'URL via la résolution de modèle.
            // 'patient_id' n'est pas attendu non plus, il est dérivé de la demande 'Analyse' parente.
            // 'labtechnician_id' n'est pas attendu non plus, il vient de l'utilisateur authentifié.

            'lab_id' => ['required', 'integer', 'exists:laboratories,id'], // Correction du nom de table si 'laboratorys' est 'laboratories'
            'analyse_type' => ['required', 'string', 'max:255'],

            'resultat_text' => ['nullable', 'string'], // Rend le texte optionnel
            'result_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'], // Accepte un fichier, max 5MB
            // Un des deux (resultat_text ou result_file) devrait être requis. On peut utiliser bail.
            // 'resultat_text' => ['required_without:result_file', 'string'],
            // 'result_file' => ['required_without:resultat_text', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],

            'comments' => ['nullable', 'string', 'max:1000'], // Augmenté la taille pour les commentaires
            // 'status' est défini comme 'completed' dans le contrôleur, donc pas 'required' ici.
            // Si vous voulez le laisser configurable, utilisez Rule::in(['pending', 'validated', 'rejected'])
            'status' => ['nullable', Rule::in(['pending', 'validated', 'rejected', 'completed'])], // Rendu nullable si contrôleur le fixe

            // 'validated_at' devrait être optionnel ou seulement requis quand le statut est 'validated'
            'validated_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'lab_id.required' => 'L’identifiant du laboratoire est requis.',
            'lab_id.integer' => 'L’identifiant du laboratoire doit être un nombre entier.',
            'lab_id.exists' => 'Le laboratoire spécifié est introuvable.',

            'analyse_type.required' => 'Le type d’analyse est obligatoire.',
            'analyse_type.string' => 'Le type d’analyse doit être une chaîne de caractères.',
            'analyse_type.max' => 'Le type d’analyse ne doit pas dépasser 255 caractères.',

            'resultat_text.string' => 'Le résultat texte doit être une chaîne de caractères.',

            'result_file.file' => 'Le résultat doit être un fichier valide.',
            'result_file.mimes' => 'Le fichier doit être de type : pdf, doc, docx, jpg, jpeg, png.',
            'result_file.max' => 'La taille du fichier ne doit pas dépasser 5 Mo.',
            // 'resultat_text.required_without' => 'Un résultat texte ou un fichier est requis.',
            // 'result_file.required_without' => 'Un résultat texte ou un fichier est requis.',

            'comments.string' => 'Les commentaires doivent être une chaîne de caractères.',
            'comments.max' => 'Les commentaires ne doivent pas dépasser 1000 caractères.',

            'status.in' => 'Le statut doit être "pending", "validated", "rejected" ou "completed".',

            'validated_at.date' => 'La date de validation doit être une date valide.',
            'validated_at.after_or_equal' => 'La date de validation doit être aujourd’hui ou une date ultérieure.',
        ];
    }
}
