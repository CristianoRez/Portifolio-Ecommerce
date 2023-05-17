<?php

namespace App\Http\Controllers;

use App\Models\Order;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function GeneratePDF(Request $request)
{
    $order = Order::with('user')->with('cart')->where('user_id', Auth::user()->id)->where('id', $request['order_id'])->first();
    $pdfData = [
        'order_id' => $order->id,
        'order_date' => $order->created_at,
        'price' => $order->price,
        'user_name' => $order->user->name,
        'user_mail' => $order->user->email,
        'user_state' => $order->user->adress->state,
        'user_city' => $order->user->adress->city,
        'user_neighborhood' => $order->user->adress->neighborhood,
        'user_cep' => $order->user->adress->cep,
        'user_street' => $order->user->adress->street,
        'user_street_number' => $order->user->adress->number,
        'transport_type' => 'PAC',
        'payment_type'  => 'Transferência Bancária',
        'product_sum_value' => $order->price,
        'logistic_value' => 50,
        'total_price' => $order->price + 50,
        'products' => [],
    ];

    foreach ($order->cart as $cart) {
        $product = $cart->product;
        $pdfData['products'][] = [
            'description' => $product->description,
            'price' => $product->price,
            'qty' => $cart->quantity,
            'grand_total' => $product->price * $cart->quantity
        ];
    }

    $pdf = PDF::loadView('invoice_pdf', compact('pdfData'));
    return $pdf->download('invoicePDF.pdf');
}
}
