<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'payment_number' => $this->payment_number,
            'payment_token' => $this->payment_token,
            'payment_time' => $this->created_at,
            'booking' => new Bookingresource($this->booking),
            'user' => new UserResource($this->user)
        ];
    }
}
