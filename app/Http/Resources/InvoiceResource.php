<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ConsulationCollection;
use App\Http\Resources\PaymentCollection;
use App\Http\Resources\PatientCollection;

class InvoiceResource extends JsonResource
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
            'hospital_id'=>$this->hospital_id,
            'users'=>$this->user_id,
            'amount'=> $this->amount,
            'status' => $this->status,
            'paid_date'=> $this->paid_date,
            'notes' => $this->notes,
            'consultations' => new ConsulationCollection($this->whenLoaded('consultations')),
            'payments'=> new PaymentCollection($this->whenLoaded('payemnts')),
            'patients'=> new PatientCollection($this->whenLoaded('patients')),
        ];
    }
}
