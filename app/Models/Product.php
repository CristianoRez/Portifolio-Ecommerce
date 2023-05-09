<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'promotion_price',
        'description',
        'stock',
        'height',
        'width',
        'weight',
        'lenght',
        'photo'
    ];
    
}
