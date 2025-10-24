<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DoctorCollection;
use App\Http\Resources\NurseCollection;
use App\Http\Resources\LaboratoryCollection;
use App\Http\Resources\HospitalCollection;
class DepartmentResource extends JsonResource
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
            'name'=> $this->name,
            'description'=> $this->description,
            'status'=> $this->status,
            'position'=> $this->position,
            'doctors'=> new DoctorCollection($this->whenLoaded('doctors')),
            'nurses' => new NurseCollection($this->whenLoaded('nurses')),
            'laboratories' =>new  LaboratoryCollection($this->whenLoaded('laboratories')),
            'hospitals' => new HospitalCollection($this->whenLoaded('hospitals')),
        ];
    }
}
