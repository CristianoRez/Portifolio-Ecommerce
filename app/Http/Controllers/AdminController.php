<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function SoldProducts()
    {
        return Order::with('bag')->get();
    }
    public function DashBoardList(Request $request)
    {
        $return = [
            'Products-Sold' => $this->MonthSoldProducts($request),
            'Gross-Year-Money' => $this->YearGrossMoney($request),
            'Gross-Month-Money' => $this->MonthGrossMoney($request),
            'Total-Gross-Money' => $this->GrossValue()
        ];
        return $return;
    }

    public function GrossValue()
    {
        $orders = Order::all();
        $count = count($orders);

        $totalPrice = 0;
        for ($i = 0; $i < $count; $i++) {
            $add = $orders[$i]['price'];
            $totalPrice += $add;
        }
        return $totalPrice;
    }
    public function MonthGrossMoney($request)
    {
        $primeiroDiaDoMes = Carbon::create($request['year'], $request['month'])->startOfMonth();
        $ultimoDiaDoMes = Carbon::create($request['year'], $request['month'])->endOfMonth();
        $orders = Order::whereDate('created_at', '>=', $primeiroDiaDoMes)->whereDate('created_at', '<=', $ultimoDiaDoMes)->get();
        $count = count($orders);

        $totalMonthPrice = 0;
        for ($i = 0; $i < $count; $i++) {
            $add = $orders[$i]['price'];
            $totalMonthPrice += $add;
        }
        return $totalMonthPrice;
    }

    public function YearGrossMoney($request)
    {
        $startDay = Carbon::create($request['year'])->startOfYear();
        $endDay = Carbon::create($request['year'])->endOfYear();
        $orders = Order::whereDate('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
        $ordersCount = count($orders);
        $grossMoney = 0;

        for ($i = 0; $i < $ordersCount; $i++) {
            $grossMoney += $orders[$i]['price'];
        }

        return $grossMoney;
    }

    public function MonthSoldProducts($request)
    {
        $startDay = Carbon::create($request['year'], $request['month'])->startOfMonth();
        $endDay = Carbon::create($request['year'], $request['month'])->endOfMonth();
        $order = Order::with('Bag')->where('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
        $totalQuantity = 0;
        for ($i = 0; $i < count($order); $i++) {
            if ($order[$i]['bag'] && $order[$i]['bag']['quantity'] > 0) {
                $productQuantity = $order[$i]['bag']['quantity'];
                $totalQuantity += $productQuantity;
            }
        }
        return $totalQuantity;
    }

    public function ProductsStatus(Request $request)
    {
        return Order::where('status', $request['status'])->get();
    }

    public function TransportProducts(Request $request)
    {
        return Order::where('id', $request['order_id'])->update(['status' => 3]);
    }
    public function DeliveredProducts(Request $request)
    {
        return Order::where('id', $request['order_id'])->update(['status' => 4]);
    }
    public function exportOrders()
    {
        // $orders = Order::all();
        // return Excel::download(new OrdersExport);
    }
}
