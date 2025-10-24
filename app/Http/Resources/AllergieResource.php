<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PatientCollection;
class AllergieResource extends JsonResource
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
            'medical_record_id'=> $this->medicalRecords->id,
            'patient_id'=> $this->patients->id,
            'substance'=> $this->substance,
            'reaction_description'=> $this->reaction_description,
            'serverity'=> $this->serverity,
            'recorded_by'=> $this->recorded_by,
            'status'=> $this->status,
            'notes'=>$this->notes,
            'patient'=> new PatientCollection($this->whenLoaded('patients')),
        ];
    }
}
