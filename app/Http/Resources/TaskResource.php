<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this -> id,
            'user_id' => $this ->user_id,
            'title' => $this ->title,
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this ->due_date ? $this-> due_date->format('d/m/Y') : null,
            'created_at' => $this->created_at ->format('d/m/Y'),
            'updated_at' => $this->updated_at ->format('d/m/Y'),
        ];
    }
}



