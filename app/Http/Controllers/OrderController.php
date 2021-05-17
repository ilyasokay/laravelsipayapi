<?php

namespace App\Http\Controllers;

use App\Helpers\Sipay;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('invoice')->get();

        return view('order.index')
            ->with('orders', $orders);
    }

    public function checkStatus($invoice_id)
    {
        $getToken = Sipay::getToken();

        $inputs = [
            "merchant_key" => config('payment.sipay.api_merchant_key'),
            "invoice_id" => $invoice_id,
            "include_pending_status" => true
        ];

        $checkStatus = Sipay::checkStatus($getToken->token, $inputs);

        return redirect()->route('order.index')
            ->with('checkStatus', $checkStatus);
    }

    public function getStatus($invoice_id)
    {
        $getToken = Sipay::getToken();

        $inputs = [
            "merchant_key" => config('payment.sipay.api_merchant_key'),
            "invoice_id" => $invoice_id,
            "is_direct_bank" => 1
        ];

        $getStatus = Sipay::checkStatus($getToken->token, $inputs);

        return redirect()->route('order.index')
            ->with('getStatus', $getStatus);
    }

    public function refund($order_id)
    {
        $getToken = Sipay::getToken();

        $order = Order::with(['invoice', 'items'])->find($order_id);

        $inputs = [
            "merchant_key" => config('payment.sipay.api_merchant_key'),
            "invoice_id" => $order->invoice->id,
            "amount" => $order->items->sum('price')
        ];

        $getRefund = Sipay::checkStatus($getToken->token, $inputs);

        if($getRefund->status_code == 100){
            $order->status = Order::STATUS_REFUND;
            $order->save();
        }

        return redirect()->route('order.index')
            ->with('getRefund', $getRefund);
    }
}
