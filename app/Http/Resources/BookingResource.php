<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'booked_from' => $this->booked_from,
            'booked_to' => $this->booked_to,
            'status' => $this->status == 0 ? 'inactive' : 'active',
            'total_price' => $this->total_price,
            'car' => new CarResource($this->car),
        ];
    }
}
