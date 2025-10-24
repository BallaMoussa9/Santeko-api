<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\AppointmentCollection;
use App\Http\Resources\MedicalRecordCollection;
use App\Http\Resources\AnalysesCollection;
use App\Http\Resources\AllergieCollection;
use App\Http\Resources\VaccinationCollection;
use App\Http\Resources\AnalysesRequestCollection;
use App\Http\Resources\InvoiceCollection;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_numero_dossier' => $this->medical_record_id,
            'group-sanguin' => $this->group_sanguin,
            'genre' => $this->genre,
            'telephone_urgence' => $this->telephone_urgence,
            'maladies_chroniques' => $this->maladies_chroniques,
            'numero_urgence' => $this->numero_urgence,
            'assurance_maladie' => $this->assurance_maladie,
            'poids' => $this->poids,
            'taille' => $this->taille,
            'user' => new UserResource($this->whenLoaded('user')),
            'appointments' => new AppointmentCollection($this->whenLoaded('appointments')),
            'medicalrecords' => new MedicalRecordCollection($this->whenLoaded('medicalrecords')),
            'analyses' => new AnalysesCollection($this->whenLoaded('analyses')),
            'allergies' => new AllergieCollection($this->whenLoaded('allergies')),
            'vaccinations' => new VaccinationCollection($this->whenLoaded('vaccinations')),
            'analyserequest' => new AnalysesRequestCollection($this->whenLoaded('analysesrequests')),
            'invoices' => new InvoiceCollection($this->whenLoaded('invoices')), // ✅ corrigé ici
            'rendezvous_count' => $this->rendezvous->count(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
