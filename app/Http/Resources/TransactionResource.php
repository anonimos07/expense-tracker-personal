<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'amount'      => (float) $this->amount,
            'description' => $this->description,
            'date'        => $this->date->format('Y-m-d'),
            'notes'       => $this->notes,
            'wallet_id'   => $this->wallet_id,
            'category_id' => $this->category_id,
            'wallet'      => $this->whenLoaded('wallet', fn () => [
                'id'   => $this->wallet->id,
                'name' => $this->wallet->name,
                'type' => $this->wallet->type,
            ]),
            'category'    => $this->whenLoaded('category', fn () => $this->category ? [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'type'  => $this->category->type,
                'color' => $this->category->color,
                'icon'  => $this->category->icon,
            ] : null),
            'created_at'  => $this->created_at,
        ];
    }
}