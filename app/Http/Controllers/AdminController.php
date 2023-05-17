<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\Chat;
use App\Models\Direct_message;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function SoldProducts()
    {
        if (Auth::user()->nivel == 2) {
            return Order::with('bag')->get();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function DashBoardList(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            $return = [
                'Products-Sold' => $this->MonthSoldProducts($request),
                'Gross-Year-Money' => $this->YearGrossMoney($request),
                'Gross-Month-Money' => $this->MonthGrossMoney($request),
                'Total-Gross-Money' => $this->GrossValue(),
            ];
            return $return;
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function GrossValue()
    {
        if (Auth::user()->nivel == 2) {
            $orders = Order::all();
            $count = count($orders);

            $totalPrice = 0;
            for ($i = 0; $i < $count; $i++) {
                $add = $orders[$i]['price'];
                $totalPrice += $add;
            }
            return $totalPrice;
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function MonthGrossMoney($request)
    {
        if (Auth::user()->nivel == 2) {
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
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function YearGrossMoney($request)
    {
        if (Auth::user()->nivel == 2) {
            $startDay = Carbon::create($request['year'])->startOfYear();
            $endDay = Carbon::create($request['year'])->endOfYear();
            $orders = Order::whereDate('created_at', '>=', $startDay)->where('created_at', '<=', $endDay)->get();
            $ordersCount = count($orders);
            $grossMoney = 0;

            for ($i = 0; $i < $ordersCount; $i++) {
                $grossMoney += $orders[$i]['price'];
            }

            return $grossMoney;
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function MonthSoldProducts($request)
    {
        if (Auth::user()->nivel == 2) {
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
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function ProductsStatus(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            return Order::where('status', $request['status'])->get();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function TransportProducts(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            return Order::where('id', $request['order_id'])->update(['status' => 3]);
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function DeliveredProducts(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            return Order::where('id', $request['order_id'])->update(['status' => 4]);
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function exportOrders()
    {
        if (Auth::user()->nivel == 2) {
            // $orders = Order::all();
            // return Excel::download(new OrdersExport);
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function SeeQuestions()
    {
        if (Auth::user()->nivel == 2) {
            $latestChats = Chat::with('user')->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('chats')
                    ->groupBy('user_id');
            })->get();

            return $latestChats;
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function SeeUserQuestion(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            return Chat::where('user_id', $request['user_id'])->get();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function AnswerQuestion(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            $chat = Chat::where('user_id', $request['user_id'])->latest()->first();
            if ($chat->response != null) {
                Chat::create([
                    'user_id' => $chat->user_id,
                    'text' => $chat->text,
                    'response' => $request['response']
                ]);
            } else {
                $chat->response = $request['response'];
                $chat->save();
            }
            return Chat::latest()->first();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }
    public function AdminOpenChat(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            $query = [
                'admin_id' => Auth::user()->id,
                'user_id' => $request['user_id']
            ];
            $chat = Chat::where($query)->get();
            if ($chat->isEmpty()) {
                return Chat::with('user_id')->create(['admin_id' => Auth::user()->id, 'user_id' => $request['user_id']]);
            } else {
                return Chat::with('user_id')->with('direct_message')->where($query)->first();
            }
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function AdminSendMessage(Request $request)
    {
        if (Auth::user()->nivel == 2) {
            Direct_message::create([
                'message' => $request['message'],
                'chat_id' => $request['chat_id'],
                'sender' => true
            ]);
            return Chat::with('direct_message')->where('id', $request['chat_id'])->first();
        } else {
            return response()->json(["message" => "User doesn't have permission"], 403);
        }
    }

    public function ShowAdminChats()
    {
        return Chat::with('user_id')->with('last_direct_message')->where('admin_id', Auth::user()->id)->get();        
    }
}
