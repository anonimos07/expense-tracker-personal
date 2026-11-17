<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = ['category_id', 'amount', 'month'];

    protected $casts = [
        'amount' => 'decimal:2',
        'month'  => 'date:Y-m-d',
    ];

    public function user(){

         return $this->belongsTo(User::class); 
    }

    public function category(){ 

        return $this->belongsTo(Category::class); 

    }
    
    use HasFactory;
}
