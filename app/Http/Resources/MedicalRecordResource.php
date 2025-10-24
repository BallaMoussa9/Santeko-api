<?php

namespace App\Http\Resources;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\AllergieCollection;
use App\Http\Resources\ConsultationHistoryCollection;
use App\Http\Resources\VaccinationCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id ' => $this->id,
            'doctor_id' =>$this->doctor_id,
            'blood_type'=>$this->blood_type,
            'treatment_plan'=>$this->treatment_plan,
            'diagnosis' => $this->diagnosis,
            'chronic_conditions'=> $this->chronic_conditions,
            'status'=> $this->status,
            'patients' => new PatientCollection($this->whenLoaded('patients')),
            'allergies'=> new AllergieCollection($this->whenLoaded('allergies')),
            'consultationHistorys' =>new ConsultationHistoryCollection($this->whenLoaded('consultationHistorys')),
            'vaccinations' => new VaccinationCollection($this->whenLoaded('vaccinations')),
        ];
    }
}
