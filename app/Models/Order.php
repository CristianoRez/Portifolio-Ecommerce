<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'price',
        'user_id',
        'adress_id',
        'status'
    ];
    public function user(){
        return $this->hasone(User::class, "id", "user_id")->with('adress');
    }
    public function cart(){
        return $this->hasMany(Cart::class, 'order_id', 'id')->with('product');
    }  
}
