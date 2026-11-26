<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['name', 'type', 'initial_balance', 'currency'];

    protected $casts = ['initial_balance' => 'decimal:2'];

    public function user(){

         return $this->belongsTo(User::class); 

    }

    public function transactions(){ 
        
        return $this->hasMany(Transaction::class); 
        
    }

    public function scopeWithBalance(Builder $query): Builder
    {
        return $query
            ->withSum(['transactions as income_total' => fn ($q) => $q->where('type', 'income')], 'amount')
            ->withSum(['transactions as expense_total' => fn ($q) => $q->where('type', 'expense')], 'amount');
    }

    public function getBalanceAttribute(): float
    {
        return round(
            (float) $this->initial_balance
            + (float) ($this->income_total ?? 0)
            - (float) ($this->expense_total ?? 0),
            2
        );
    }
    
    use HasFactory;
    
}
