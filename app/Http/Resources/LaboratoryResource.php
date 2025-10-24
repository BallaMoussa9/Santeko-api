<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AnalysesCollection;
use App\Http\Resources\LabTechniciansCollection;
use App\Http\Resources\AnalysesRequestCollection;
use App\Http\Resources\DepartmentCollection;

class LaboratoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'name'=> $this->name,
            'adress' => $this->adress,
            'phone' => $this->phone,
            'email' => $this->email,
            'opening_time'=> $this->opening_time,
            'closing_time'=> $this->closing_time,
            'status'=> $this->status,
            'analyses' => new AnalysesCollection($this->whenLoaded('analyses')),
            'labTechnicians' => new LabTechniciansCollection($this->whenLoaded('labTechnicians')),
            'analyserequest' => new AnalysesRequestCollection($this->whenLoaded('analyserequest')),
            'departments' => new DepartmentCollection($this->whenLoaded('departments')),
                ];
    }
}
