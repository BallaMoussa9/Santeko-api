<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'speciality' => $this->speciality,
            'numero_ordre' => $this->numero_ordre,
            'biography' => $this->biography,
            'experience' => $this->experience,
            'status' => $this->status,
            'numero_professionel' => $this->numero_professionel,

            // Relations 1-to-1
            'user' => new UserResource($this->whenLoaded('user')),
            'department' => new DepartmentResource($this->whenLoaded('department')),

            // Relations HasMany
            'teleconsultations' => new TeleconsultationCollection($this->whenLoaded('teleconsultations')),
            'consultations' => new ConsultationCollection($this->whenLoaded('consultations')),
            'medicalreports' => new MedicalReportCollection($this->whenLoaded('medicalreports')),

            // Quelques infos directes de l'utilisateur lié
            'first_name' => $this->whenLoaded('user', fn () => $this->user->first_name),
            'last_name' => $this->whenLoaded('user', fn () => $this->user->last_name),
            'email' => $this->whenLoaded('user', fn () => $this->user->email),
            'phone' => $this->whenLoaded('user', fn () => $this->user->phone),
            'profile_photo' => $this->whenLoaded('user', fn () => $this->user->profile_photo),
        ];
    }
}
