<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AllergieCollection;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\ConsultationCollection;
class ConsultationHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'medicalrecord_id'=> $this->medicalrecord_id,
            'department_id'=> $this->department_id,
            'doctor_id'=> $this->doctor_id,
            'date_consultation'=> $this->date_consultation,
            'type'=>$this->type,
            'motif'=>$this->motif,
            'diagnostic'=> $this->diagnostic,
            'traitement'=>$this->traitement,
            'notes'=> $this->notes,
            'last_updated_by'=> $this->last_updated_by,
            'allergies'=> new AllergieCollection($this->whenLoaded('allergies')),
            'patients' => new PatientCollection($this->whenLoaded('patients')),
            'consultations' => new ConsultationCollection($this->whenloaded('consultations')),
        ];
    }
}
