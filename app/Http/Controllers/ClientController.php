<?php

namespace App\Http\Controllers;

use App\Mail\DemoMail;
use App\Models\Adress;
use App\Models\Cart;
use App\Models\Chat;
use App\Models\Direct_message;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function SendAdress(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        return Adress::create($data);
    }

    public function ShowProducts()
    {
        return Product::get();
    }

    public function cartAddProduct($id, Request $request)
    {
        $query = [
            ['user_id', Auth::user()->id],
            ['product_id', $id],
            ['order_id', null]
        ];

        $q = Cart::where($query)->first();

        if ($q) {
            $q->update(
                ["quantity" => $request["quantity"]]
            );
            return cart::where('user_id', Auth::user()->id)->where('product_id', $id)->latest()->first();
        } else {
            return cart::create([
                "user_id" => Auth::user()->id,
                "product_id" => $id,
                "quantity" => $request["quantity"]
            ]);
        }
    }

    public function ShowCart()
    {
        $query = [
            ['user_id', Auth::user()->id],
            ['order_id', null]
        ];
        $usercart = Cart::with("product")->where($query)->get();

        if ($usercart->isEmpty()) {
            return "Erro: Não há itens no carrinho!";
        } else {
            $totalProductPrice = 0;
            foreach ($usercart as $cartItem) {
                $totalProductPrice += $cartItem->product->price * $cartItem->quantity;
            }
            $totalProductPromotionPrice = 0;
            foreach ($usercart as $cartItem) {
                $totalProductPromotionPrice += $cartItem->product->promotion_price * $cartItem->quantity;
            }
            $cart = cart::with('product')->where($query)->get();
            $return = [
                'Cart: ' => $cart,
                'Preço total: ' => $totalProductPrice,
                'Preço promocional: ' => $totalProductPromotionPrice
            ];
            return $return;
        }
    }

    public function Clearcart()
    {
        return Cart::where('user_id', Auth::user()->id)->delete();
    }

    public function ChangeQuantity(Request $request)
    {
        $query = Cart::where('user_id', Auth::user()->id)->where('product_id', $request['product_id'])->where('order_id', null)->first();
        if ($query == null) {
            return response()->json(['success' => false, 'message' => 'Esse produto não esta no carrinho']);
        } else {
            return $query->update(
                ["quantity" => $request["quantity"]]
            );
        }
    }

    public function RemoveProduct($id)
    {

        $query = [
            ['user_id', Auth::user()->id],
            ['product_id', $id]
        ];
        return Cart::where($query)->delete();
    }

    public function BuyProduct(Request $request)
    {
        //getting the client's adress's id from database.
        $adress = Adress::where('user_id', Auth::user()->id)->first();
        //If the client doesn't have an address logged in, it will return an error.
        if ($adress == null) {
            return "Erro: Endereço não informado!";
        }

        //The $usercart variable will receive all the products with their data that are in the cart table that have the same 'user_id' as the logged in user and the 'order_id' field with a null value.
        $query = [
            ['user_id', Auth::user()->id],
            ['order_id', null]
        ];
        $usercart = Cart::with("product")->where($query)->get();

        if ($usercart->isEmpty()) {
            return "Erro: Não há itens pendentes no carrinho!";
        } else if ($request['price'] == 1) {
            //Here we define the value of the $totalProductPrice variable as 0, so that each time the BuyProduct function is used we can make a new addition, in order to obtain a new and correct price value corresponding only to the new products added.
            $totalProductPrice = 0;

            //For each product inside the $usercart variable, an operation will be performed to request its price and add it to a total value.
            foreach ($usercart as $cartItem) {
                //$new Stock will receive the stock value of the referenced product minus the quantity of the product chosen by the client for his cart.
                $NewStock = Product::where('id', $cartItem->product_id)->first()->stock - $cartItem->quantity;

                //The product stock value will be changed to the new value
                Product::where('id', $cartItem->product_id)->update(['stock' => $NewStock]);

                //Each time the command is executed, the value of multiplying the price by the quantity of the product purchased is added to the value of $totalProductPrice;
                $totalProductPrice += $cartItem->product->price * $cartItem->quantity;
            }
            $userOrder = Order::create(
                [
                    "user_id" => Auth::user()->id,
                    "adress_id" => $adress['id'],
                    "price" => $totalProductPrice,
                    "status" => 2
                ]
            );
            //This command will update the 'order_id' field with the $userOrder variable to specify which products in the cart have already been purchased by the client.
            cart::where('user_id', Auth::user()->id)->where('order_id', null)->update(['order_id' => $userOrder['id']]);
        } else if ($request['price'] == 2) {
            //Here we define the value of the $totalProductPrice variable as 0, so that each time the BuyProduct function is used we can make a new addition, in order to obtain a new and correct price value corresponding only to the new products added.
            $totalProductPrice = 0;

            //For each product inside the $usercart variable, an operation will be performed to request its price and add it to a total value.
            foreach ($usercart as $cartItem) {
                //$new Stock will receive the stock value of the referenced product minus the quantity of the product chosen by the client for his cart.
                $NewStock = Product::where('id', $cartItem->product_id)->first()->stock - $cartItem->quantity;

                //The product stock value will be changed to the new value
                Product::where('id', $cartItem->product_id)->update(['stock' => $NewStock]);

                //Each time the command is executed, the value of multiplying the price by the quantity of the product purchased is added to the value of $totalProductPrice;
                $totalProductPrice += $cartItem->product->promotion_price * $cartItem->quantity;
            }

            //In the Order table, a row will be created representing the client's order, with the fields: user id; user address id; and the total amount that the client will pay, that was calculated in foreach.


            //This variable will get the client's order's id
            $userOrder = Order::create(
                [
                    "user_id" => Auth::user()->id,
                    "adress_id" => $adress['id'],
                    "price" => $totalProductPrice,
                    "status" => 2
                ]
            );
            //This command will update the 'order_id' field with the $userOrder variable to specify which products in the cart have already been purchased by the client.
            cart::where('user_id', Auth::user()->id)->where('order_id', null)->update(['order_id' => $userOrder['id']]);
        }
        //sending email confirmation:
        //index()
        $order = Order::with('cart')->where('user_id', Auth::user()->id)->where('status', 2)->latest()->first();
        $count = count($order->cart);

        $allProductsDescription = [];
        for ($i = 0; $i < $count; $i++) {
            $productDescription = $order->cart[$i]['product']['description'];
            $allProductsDescription[] = ['Description' => $productDescription];
        }

        $url = url('/app/photos/' . $order->cart[0]['product']['photo']);
        $quantity = 0;
        for ($i = 0; $i < $count; $i++) {
            $quantity += $order->cart[$i]['quantity'];
        }

        $productDescriptionString = '';
        foreach ($allProductsDescription as $product) {
            $productDescriptionString .= $product['Description'] . ', ';
        }
        $productDescriptionString = rtrim($productDescriptionString, ', '); // Remova a última vírgula e espaço

        $mailData = [
            'title' => 'A sua compra foi efetuada com sucesso.',
            'body' => 'Confira abaixo seu pedido: ' . $productDescriptionString, // Adicione a variável aqui
            'quantity' => $quantity,
            'price' => $totalProductPrice,
            'img' => $url
        ];
        $mail = Auth::user()->email;
        Mail::to($mail)->send(new DemoMail($mailData));

        $message = [
            'Email' => 'Você receberá um email com a confirmação da compra',
            'quantity' => $quantity,
            'price' => $totalProductPrice,
            'img' => $url
        ];
        $message['productDescription'] = $productDescriptionString; // Adicione a variável aqui
        return $message;
    }

    public function LogisticCalc(Request $request)
    {

        $code = [40010, 41106];
        $originZipCode = "80410210";
        $destinationZipCode = str_replace('-', '', $request['cep']);

        //Calc of weight
        $cartWithProducts = Cart::with('product')
            ->where('user_id', Auth::user()->id)
            ->where('order_id', null)
            ->get();
        $totalWeight = 0;
        for ($i = 0; $i < count($cartWithProducts); $i++) {
            $totalWeight += $cartWithProducts[$i]['product']['weight'] * $cartWithProducts[$i]['quantity'];
        }

        //Calc of total Height
        $totalHeight = 0;
        for ($i = 0; $i < count($cartWithProducts); $i++) {
            $totalHeight += $cartWithProducts[$i]['product']['height'] * $cartWithProducts[$i]['quantity'];
        }
        if ($totalHeight < 10) {
            $totalHeight = 10;
        }

        //Calc of greater width
        $widths = array();
        for ($i = 0; $i < count($cartWithProducts); $i++) {
            $width = $cartWithProducts[$i]['product']['width'];
            $widths[] = $width;
        }
        $greaterWidth = max($widths);
        if ($greaterWidth < 10) {
            $greaterWidth = 10;
        }

        //calc of greater length
        $lengths = array();
        for ($i = 0; $i < count($cartWithProducts); $i++) {
            $length = $cartWithProducts[$i]['product']['lenght'];
            $lengths[] = $length;
        }
        $greaterLength = max($lengths);
        if ($greaterLength < 10) {
            $greaterLength = 10;
        }

        // //----------------------------------------------------------------------------------------
        $url = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';

        if ($length < 16) {
            $length = 16;
        }


        $params = [
            'nCdEmpresa' => '',
            'sDsSenha' => '',
            'sCepOrigem' => $originZipCode,
            'sCepDestino' => $destinationZipCode,
            'nVlPeso' => $totalWeight,
            'nCdFormato' => '1',  //1 para caixa / pacote e 2 para rolo/prisma.
            'nVlComprimento' => $greaterLength,
            'nVlAltura' => $totalHeight,
            'nVlLargura' => $greaterWidth,
            'nVlDiametro' => '0',
            'sCdMaoPropria' => 'n',
            'nVlValorDeclarado' => '0',
            'sCdAvisoRecebimento' => 'n',
            'StrRetorno' => 'xml',
            'nCdServico' =>  $code[0],
        ];
        $params2 = $params;
        $params = http_build_query($params);

        $curl = curl_init($url . '?' . $params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $data = simplexml_load_string($data);

        $jsonEncodeData = json_decode(json_encode($data));
        $sedex = [
            'value' => $jsonEncodeData->cServico->Valor,
            'prazo' => $jsonEncodeData->cServico->PrazoEntrega,
        ];

        $params2['nCdServico'] = $code[1];

        $params2 = http_build_query($params2);

        $curl = curl_init($url . '?' . $params2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        $data = simplexml_load_string($data);

        $jsonEncodeData = json_decode(json_encode($data));
        $sedex2 = [
            'value' => $jsonEncodeData->cServico->Valor,
            'prazo' => $jsonEncodeData->cServico->PrazoEntrega,
        ];
        $BothSedex = ['sedex' => $sedex, 'pac' => $sedex2];
        return $BothSedex;
    }
    //-------------------------------------------------------------------------------------

    public function UserOpenChat(Request $request)
    {
        $query = [
            'user_id' => Auth::user()->id,
            'admin_id' => $request['admin_id']
        ];
        $chat = Chat::where($query)->get();
        if ($chat->isEmpty()) {
            Chat::create(['user_id' => Auth::user()->id, 'admin_id' => $request['admin_id']]);
            return Chat::with('Admin_id')->with('direct_message')->where($query)->first();
        } else {
            return Chat::with('admin_id')->with('direct_message')->where($query)->first();
        }
    }

    public function UserSendMessage(Request $request)
    {
        Direct_message::create([
            'message' => $request['message'],
            'chat_id' => $request['chat_id'],
            'sender' => false
        ]);
        return Chat::with('direct_message')->where('id', $request['chat_id'])->first();
    }

    public function ShowUserChats()
    {
        return Chat::with('admin_id')->with('last_direct_message')->where('user_id', Auth::user()->id)->get();
    }
}
