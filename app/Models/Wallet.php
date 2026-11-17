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
    
    use HasFactory;
    
}
