<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AnalysesCollection;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\LaboratoryCollection;

class AnalysesRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' =>$this->id,
            'labtechnician_id' => $this->labtechnician_id,
            'result_text'=> $this->result_text,
            'result_file'=> $this->result_file,
            'status'=> $this->status,
            'analyse_type'=>$this->analyse_type,
            'validated_at'=>$this->validated_at,
            'comments'=>$this->comments,
            'analyses' => new AnalysesCollection($this->whenLoaded('analyses')),
            'patients'=> new PatientCollection($this->whenloaded('patient')),
            'laboratories' => new  LaboratoryCollection($this->whenLoaded('laboratories')),
        ];
    }
}
