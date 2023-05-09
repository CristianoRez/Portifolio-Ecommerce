<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function ProductList()
    {
        return Product::get();
    }
    public function ProductInfo($id){
        return Product::where('id', $id)->first();
    }
}
