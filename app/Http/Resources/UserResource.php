<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\RoleResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\LanguageResource;
use App\Http\Resources\PatientResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\FirstResponderResource;
use App\Http\Resources\LabTechnicianResource;

class UserResource extends JsonResource
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
            'firstname' => $this->first_name,
            'lastname' => $this->last_name,
            'birthdate' => $this->birth_date,
            'phone' => $this->phone,
            'country' => $this->country,
            'city' => $this->city,
            'profile_photo' => $this->profile_photo,
            'status' => $this->status,
            'address' => $this->address,
            'email' => $this->email,
            // Relations : attention, toutes ces relations doivent être bien définies dans le modèle User
            'patients' => PatientResource::collection($this->whenLoaded('patients')),
            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
    ];
}
}
