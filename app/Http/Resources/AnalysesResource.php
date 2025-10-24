<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AnalysesRequestResource;
use App\Http\Resources\LaboratoryCollection;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\LabTechniciansCollection;

class AnalysesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'consultation_id'=> $this->consultation_id,
            'labtechnicians_id'=> $this->labtechnicians_id,
            'name'=>$this->name,
            'type'=>$this->type,
            'status'=>$this->status,
            'analyseRequests'=> new AnalysesRequestResource($this->whenLoaded('analyseRequests')),
            'laboratorys'=> new LaboratoryCollection($this->whenLoaded('laboratorys')),
            'patients' => new PatientCollection($this->whenLaoded('patients')),
            'labTechnicians' => new LabTechniciansCollection($this->whenLoaded('labTechnicians')),

        ];
    }
}
