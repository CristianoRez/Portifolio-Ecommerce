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
    public function ProductInfo($id)
    {
        return Product::where('id', $id)->first();
    }
    public function SearchProduct(Request $request)
    {
        $keywords = explode(' ', $request->input('keywords'));

        $products = Product::where(function ($query) use ($keywords) {
            foreach ($keywords as $keyword) {
                $query->orWhereRaw('MATCH(description) AGAINST(? IN BOOLEAN MODE)', [$keyword]);
            }
        })
            ->orderByRaw('MATCH(description) AGAINST(?) DESC', [$request->input('keywords')])
            ->get();

        return $products;
    }
}
