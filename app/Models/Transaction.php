<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $fillable = [
        'wallet_id', 'category_id', 'type',
        'amount', 'description', 'date', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date:Y-m-d',
    ];

    public function user(){ 

        return $this->belongsTo(User::class); 

    }
    public function wallet(){ 

        return $this->belongsTo(Wallet::class); 

    }
    public function category(){ 

        return $this->belongsTo(Category::class); 

    }

    public function scopeExpenses($q){

        return $q->where('type', 'expense'); 
    }

    public function scopeIncome($q){ 

        return $q->where('type', 'income'); 
    }

    public function scopeForMonth($q, $year, $month)
    {

        return $q->whereYear('date', $year)->whereMonth('date', $month);

    }
    
    use HasFactory;
}
