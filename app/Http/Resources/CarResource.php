<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'id' => (string) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'number_plate' => $this->number_plate,
            'horsepower' => $this->horsepower,
            'mileage' => $this->mileage,
            'price' => $this->price,
            'links' => [
                'image' => $this->image ? $this->image : 'image not found'
            ]
        ];
    }
}
