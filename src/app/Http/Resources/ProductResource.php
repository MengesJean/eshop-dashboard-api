<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $user = $request->user();
        $isRestricted = $user?->can('access-restricted-dashboard');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => !$isRestricted ? (float) $this->price : null,
            'sku' => $this->sku,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'stock' => $this->stock,
            'active' => $this->active,
            'weight' => (float) $this->weight,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
