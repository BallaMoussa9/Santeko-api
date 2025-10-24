<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDmeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autoriser seulement si l'utilisateur connecté est le médecin qui correspond à {doctorId}
        // OU si c'est un admin. Pour l'instant, mettons true pour le test.
        // Une implémentation réelle vérifierait Auth::id() == $this->route('doctorId') ou une politique.
        return true;
    }

    public function rules(): array
    {
        return [
            'date_consultation' => ['required', 'date_format:Y-m-d H:i:s'],
            'type' => ['required', 'string', 'max:255'],
            'motif' => ['required', 'string', 'max:255'],
            'diagnostic' => ['nullable', 'string'],
            'traitement' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
