<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = false;

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
            'name' => $this->name,
            'first_name' => $this->first_name, // Champ "first_name"
            'email' => $this->email,
            'phone_number' => $this->phone_number, // Champ "phone_number"
            'address' => $this->address, // Champ "address"
            'city' => $this->city, // Champ "city"
            'postal_code' => $this->postal_code, // Champ "postal_code"
            'siret_number' => $this->siret_number, // Champ "siret_number"
            'available_space' => $this->available_space, // Champ "available_space"
            'email_verified_at' => $this->email_verified_at, // Champ "available_space"
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
        
    }
}
