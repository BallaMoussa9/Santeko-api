<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\RegionCollection;
class FirstResponderResource extends JsonResource
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
            'status' => $this->status,
            'location'=> $this->location,
            'users'=> new UserCollection($this->whenLoaded('users')),
            'regions' => new RegionCollection($this->whenLoaded('regions')),
        ];
    }
}
