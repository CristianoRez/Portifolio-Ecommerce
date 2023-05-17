<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Product_photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
            Product::where("id", $request["id"])->update(
                [
                    "price" => $request["price"],
                    "promotion_price" => $request["promotion_price"],
                    "description" => $request["description"],
                    "stock" => $request["stock"],
                    "height" => $request["height"],
                    "width" => $request["width"],
                    "weight" => $request["weight"]
                ]
            );
            return Product::where('id', $request['id'])->first();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 401);
        }
    }

    public function UploadProductPhoto(Request $request)
    {
        $product = Product::where('id', $request['product_id'])->first();
        foreach ($request->file('photo') as $file) {
            $filename = uniqid() . 'photo' . $product['id'] . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('app/photos'), $filename);
            Product_photo::create(['name' => $filename, 'product_id' => $product['id']]);
        }
        $product->photo = $filename;
        $product->save();
    }

    public function DeleteProductPhoto(Request $request)
    {
        $photo = Product_photo::where('id', $request['photo_id'])->first();
        $product = Product::where('id', $photo->product_id)->first();
        if ($product->photo == $photo->name) {
            $product->update(['photo' => null]);
        }
        $photo->delete();

        $filename = $photo->name;
        $filePath = public_path('app/photos') . '/' . $filename;
        if (File::exists($filePath)) {
            File::delete($filePath);
            return "foto deletada!";
        } else {
            return "foto não encontrada";
        }
    }

    public function UpdateProductPhoto(Request $request)
    {
        $photo = Product_photo::where('id', $request['photo_id'])->first();
        $filePath = public_path('app/photos') . '/' . $photo->name;
        if (File::exists($filePath)){
            $request->file('photo')->move(public_path('app/photos'), $photo->name);
            return "Foto alterada com sucesso!";
        } else {
            return "A foto não existe";
        }
    }
}
