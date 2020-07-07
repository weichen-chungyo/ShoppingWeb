<?php

namespace App\Http\Controllers;

use App\Order;
use Auth;
use App\Level;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getIndex()
    {
        $orders = Order::orderBy('id', 'desc')->paginate(10);
        return view('admin/order.index', ['orders' => $orders]);
    }
    public function showOrder($id)
    {
        $order = Order::find($id);
        $user = Auth::user()->find($order->user_id);
        return view('admin/order.show', ['order' => $order,'user' => $user]);
    }
    public function sandProduct($id)
    {
        $order = Order::find($id);
        $user = Auth::user()->find($order->user_id);
        $order->status = '2' ;
        $order->pre_cashback_yn = $user->level->offer->cashback_yn ?? 'N';
        $order->pre_levelname = $user->level->name ?? '';
        $order->pre_above= $user->level->offer->cashback->above ?? 0;
        $order->pre_percent=$user->level->offer->cashback->percent ?? 0;
        if ($order->pre_cashback_yn == 'Y' && $order->total >=$order->pre_above) {
            $order->pre_dollor=round($order->total  * ($user->level->offer->cashback->percent ?? 0)) ;
        } else {
            $order->pre_dollor=0;
        }
        $order->pre_rebate_yn = $user->level->offer->rebate_yn ?? 'N';
        $order->pre_rebate_above= $user->level->offer->rebate->above ?? 0;
        if ($order->pre_rebate_yn == 'Y' && $order->total >=$order->pre_rebate_above) {
            $order->pre_rebate_dollor=$user->level->offer->rebate->rebate ?? 0 ;
        } else {
            $order->pre_rebate_dollor=0;
        }
        
        $order->save();

        return redirect()->back()->withSuccessMessage('已成功送貨');
    }
    public function refundAgree($id)
    {
        // step.1 更改商品狀態
        $order = Order::find($id);
        $order->status = '5';//已退款
        $order->save();
        // step.2 訂單退款(虛擬幣)
        $user = Auth::user()->find($order->user_id);
        $userDollor = $user->dollor->dollor;
        $userDollor = $userDollor + ($order->record);
        // step.3 虛擬幣優惠饋扣除
        $userDollor = $userDollor - ($order->pre_dollor); //虛擬幣回饋
        $userDollor = $userDollor - ($order->pre_rebate_dollor); //滿額現金
        $user->dollor->dollor= $userDollor ;
        // setp.4 會員等級重新判斷
        $user->dollor->save();
        $user->total_cost = ($user->total_cost) - ($order->total); //減去累計總消費
        $Level = $user->level_level ?? 0;
        $LevelUpgrade = Level::find($Level)->upgrade ?? 0;
        //有設定才會降等
        if (!empty($LevelUpgrade)) {
            if ($user->total_cost < $LevelUpgrade) {
                $user->level_level = $user->level_level-1;
                $user->save();
            } else {
                $user->save();
            }
        }

        return redirect()->back()->withSuccessMessage('已成功退款');
    }
    public function refundDisagree(Request $request)
    {
        // step.1 更改商品狀態
        $order = Order::find($request->orderId);
        $order->status = '6';//拒絕退款
        $order->save();
        // step.2 紀錄拒絕退貨理由
        echo $request->nomessage;
        $order->refund->nomessage = $request->nomessage;
        $order->refund->save();
        return redirect()->back()->withSuccessMessage('已拒絕退款');
    }
    public function searchOrder(Request $request)
    {
        $query = $request->input('query');
        $orders = Order::where('id', 'LIKE', '%'.$query.'%')->paginate(10);
        return view('admin/order.index', ['orders' => $orders]);
    }
    public function orderbyStatus(Request $request)
    {
        $oderbyStatus = $request->input('oderbyStatus');
        $orders = ($oderbyStatus == '0') ? Order::orderBy('id', 'desc')->paginate(10) : 
        Order::where('status',$oderbyStatus)->orderBy('id', 'desc')->paginate(10);
        
        return view('admin/order.index', ['orders' => $orders ,'oderbyStatus' => $oderbyStatus]);
    }
}
