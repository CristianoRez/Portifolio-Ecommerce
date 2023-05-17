<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'order_id'
    ];
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
