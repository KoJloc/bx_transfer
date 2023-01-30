<?php

namespace App\Http\Resources\Person;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
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
            'ID' => $this->ID,
            'SECOND_NAME' => $this->SECOND_NAME,
            'LAST_NAME' => $this->LAST_NAME,
            'ASSIGNED_BY_ID' => $this->ASSIGNED_BY_ID,
            'LEAD_ID' => $this->LEAD_ID,
        ];
    }
}
