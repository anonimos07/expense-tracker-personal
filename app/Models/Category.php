<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    protected $fillable = ['name', 'type', 'color', 'icon'];

    public function user(){ 

        return $this->belongsTo(User::class); 

    }
    public function transactions(){ 

        return $this->hasMany(Transaction::class); 

    }
    public function budgets(){ 

        return $this->hasMany(Budget::class); 

    }
    
    use HasFactory;

}
