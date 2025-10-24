<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PatientCollection;
class AppointmentResource extends JsonResource
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
            'doctor_id' => $this->doctor_id,
            'appointment_date'=> $this->appointment_date,
            'appointment_time'=> $this->appointment_time,
            'type' => $this->type,
            'motif' => $this->modif,
            'status' => $this->status,
            'cancellation_reason'=> $this->cancellation_reason,
            'comfirmed_at'=> $this->comfirmed_at,
            'completed_at'=> $this->completed_at,
            'patients' => new PatientCollection($this->whenLoaded('patients')),
        ];
    }
}
