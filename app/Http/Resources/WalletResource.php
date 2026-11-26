<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'type'            => $this->type,
            'currency'        => $this->currency,
            'initial_balance' => (float) $this->initial_balance,
            'income_total'    => (float) ($this->income_total ?? 0),
            'expense_total'   => (float) ($this->expense_total ?? 0),
            'balance'         => $this->balance,
        ];
    }
}