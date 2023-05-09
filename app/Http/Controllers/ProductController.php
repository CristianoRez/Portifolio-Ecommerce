<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Product_photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function ProductPost(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            return Product::create([
                "name" => $request["name"],
                "price" => $request["price"],
                "promotion_price" => $request["promotion_price"],
                "description" => $request["description"],
                "stock" => $request["stock"],
                "height" => $request["height"],
                "width" => $request["width"],
                "lenght" => $request["lenght"],
                "weight" => $request["weight"]
            ]);
        } else {
            return response()->json(["message" => "User doesn't have permission"], 401);
        }
    }

    public function DeletProduct($id)
    {
        $q = [
            ["id", $id]
        ];
        if (Auth::user()->nivel == 2) {
            return Product::where($q)->delete();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 401);
        }
    }

    public function UpdateProduct(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            return Product::where("id", $request["id"])->update(
                [
                    "name" => $request["name"],
                    "price" => $request["price"],
                    "promotion_price" => $request["promotion_price"],
                    "description" => $request["description"],
                    "stock" => $request["stock"],
                    "height" => $request["height"],
                    "width" => $request["width"],
                    "weight" => $request["weight"]

                ]
            );
        } else {
            return response()->json(["message" => "User doesn't have permission"], 401);
        }
    }

    public function UploadProductPhoto(Request $request)
    {
        $product = Product::where('id', $request['product_id'])->first();
        foreach ($request->file('photo') as $index => $file) {
            $filename = $index . 'photo' . $product['id'] . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('app/photos'), $filename);
            Product_photo::create(['name' => $filename, 'product_id' => $product['id']]);
        }
        $product->photo = $filename;
        $product->save();
    }
}
