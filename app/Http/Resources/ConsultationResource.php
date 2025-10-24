<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\DoctorCollection;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\TeleconsultationCollection;
use App\Http\Resources\ConsultationHistoryCollection;
class ConsultationResource extends JsonResource
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
            'prescriptions'=> new PrescriptionCollection($this->whenLoaded('prescriptions')),
            'doctors'=> new DoctorCollection($this->whenLoaded('doctors')),
            'patient'=> new PatientCollection($this->whenLoaded('patients')),
            'type'=> $this->type,
            'motif'=> $this->motif,
            'diagnostic' => $this->diagnostic,
            'status' => $this->status,
            'traitement'=>$this->traitement,
            'notes'=> $this->notes,
            'observations'=>$this->observations,
            'teleconsultations' => new TeleconsultationCollection($this->whenLoaded('teleconsultations')),
            'consultationhistorys'=> new ConsultationHistoryCollection($this->whenLoaded('consultationhistorys')),
        ];
    }
}
