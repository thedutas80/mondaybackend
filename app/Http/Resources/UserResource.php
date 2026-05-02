<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //  return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->roles->pluck('name'),
            'photo' => $this->photo ? asset($this->photo) : null,
            //'merchants' =>new MerchantResource($this->merchants),
            'merchants' => $this->merchants ? new MerchantResource($this->merchants) : null,
        ];
    }
}
