<?php

namespace App\Http\Resources\Person;

use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
        'ID' => '',
        'ASSIGNED_BY_ID' => '',
        'CONTACT_ID' => '',
        'NAME' => '',
        'LEAD_SUMMARY' => '',
        'DATE_CREATE' => '',
        'LAST_NAME' => '',
        'SECOND_NAME' => '',
        'PHONE' => '',
    ];
    }
}
