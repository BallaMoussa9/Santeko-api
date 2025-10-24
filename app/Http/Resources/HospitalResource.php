<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DepartmentCollection;
use App\Http\Resources\RegionCollection;
class HospitalResource extends JsonResource
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
            'nom' => $this->nom,
            'adresse'=> $this->adresse,
            'phone'=> $this->phone,
            'email' => $this->email,
            'ville' => $this->ville,
            'type' => $this->type,
            'departments'=> new DepartmentCollection($this->whenLoaded('departments')),
            'regions' => new RegionCollection($this->whenLoaded('regions')),
        ];
    }
}
