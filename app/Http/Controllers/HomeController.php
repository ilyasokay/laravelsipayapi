<?php

namespace App\Http\Controllers;

use App\Helpers\Sipay;
use App\Models\Bill;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    // Index
    public function index()
    {
        $getToken = Sipay::getToken();
        $is_3d = is_null($getToken) ? 0 : $getToken->is_3d;

        $products = Product::all();

        return view('home')
            ->with('products', $products)
            ->with('is_3d', $is_3d);
    }

    // Installment
    public function getPos(Request $request)
    {
        $getToken = Sipay::getToken();

        if(is_null($getToken))
        {
            return response()->json([
                "status_code" => 401,
                'message' => 'Unauthorized'
            ]);
        }

        // Required
        $inputs = [
            'credit_card' => $request->input('credit_cart', 534261),
            'amount' => $request->input('amount', '248.00'),
            'currency_code' => $request->input('currency_code', 'TRY'),
            'merchant_key' => config('payment.sipay.api_merchant_key')
        ];

        // Optional
        /*
        $inputs['is_recurring'] = 1;
        $inputs['is_2d'] = $getToken->is_3d == 0 ? 1 : 0;
        */

        $getPos = Sipay::getPos($getToken->token, $inputs);

        return response()->json([
            'data' => $getPos != null ? $getPos : []
        ]);
    }

    public function installments(Request $request)
    {
        $getToken = Sipay::getToken();

        if(is_null($getToken))
        {
            return response()->json([
                "status_code" => 401,
                'message' => 'Unauthorized'
            ]);
        }

        $getInstallments = Sipay::getInstallments($getToken->token);

        return response()->json([
            'data' => $getInstallments != null ? $getInstallments : []
        ]);
    }

    // Commissions
    public function commissions(Request $request)
    {
        $getToken = Sipay::getToken();

        $getCommissions = Sipay::getCommissions($getToken->token,[
            'currency_code' => $request->get('currency_code', 'TRY')
        ]);

        return response()->json([
            'data' => $getCommissions != null ? collect($getCommissions) : []
        ]);
    }

    // Payment
    public function payment(Request $request)
    {
        $token = Sipay::getToken();

        $invoice_id = rand(10000000001, 99999999999);
        $merchant_key = config('payment.sipay.api_merchant_key');
        $hash = Sipay::generateHashKey(5.00,1,'TRY',$merchant_key,$invoice_id,'b46a67571aa1e7ef5641dc3fa6f1712a');

        $items = [["name" => "Item3","price" => 5.00,"qnantity" => 1,"description" =>"item3 description"]];

        $inputs = [
            'cc_holder_name' => $request->input('fullname'),
            'cc_no' => $request->input('credit_card'),
            'expiry_month' => $request->input('expiry_month'),
            'expiry_year' => $request->input('expiry_year'),
            'cvv' => $request->input('cvv'),
            'currency_code' => 'TRY',
            'installments_number' => 1,
            'invoice_id' => $invoice_id,
            'invoice_description' => 'INVOICE TEST DESCRIPTION',
            'total' => 5.00,
            'merchant_key' => $merchant_key,
            'name' => 'John',
            'surname' => 'Dao',
            'hash_key' => $hash,
            'items' => $items,
        ];

        if($request->has('is_3d') && $request->input('is_3d') == 1){
            $inputs['items'] = json_encode($inputs['items'], JSON_UNESCAPED_UNICODE);
            $inputs['return_url'] = route('success');
            $inputs['cancel_url'] = route('fail');

            $paySmart3D = Sipay::paySmart3D($inputs);

            return $paySmart3D->body();
        }

        $paySmart2D = Sipay::paySmart2D($token->token, $inputs);

        if(!is_null($paySmart2D)){
            return redirect()
                ->route('index')
                ->with('success_message', 'Success..');
        }

        return redirect()
            ->route('index')
            ->with('error_message', 'Error!..');
    }

    // 3D Payment
    public function paySmart3D()
    {
        $invoice_id = rand(10000000001, 99999999999);
        $merchant_key = config('payment.sipay.api_merchant_key');
        $app_secret = config('payment.sipay.app_secret');
        $hash = Sipay::generateHashKey(5.00,1,'TRY',$merchant_key,$invoice_id,$app_secret);

        $items = [["name" => "Item3","price" => 5.00,"quantity" => 1,"description" =>"item3 description"]];

        $inputs = [
            'cc_holder_name' => 'John Dao',
            'cc_no' => 5406675406675403,
            'expiry_month' => 12,
            'expiry_year' => 2026,
            'cvv' => '000',
            'currency_code' => 'TRY',
            'installments_number' => 1,
            'invoice_id' => $invoice_id,
            'invoice_description' => 'INVOICE TEST DESCRIPTION',
            'total' => 5.00,
            'merchant_key' => $merchant_key,
            'items' => json_encode($items, JSON_UNESCAPED_UNICODE),
            'name' => 'John',
            'surname' => 'Dao',
            'hash_key' => $hash,
            'return_url' => route('success'),
            'cancel_url' => route('fail')
        ];

        $paySmart3D = Sipay::paySmart3D($inputs);

        echo $paySmart3D->body();
    }

    // Non 3D Payment
    public function paySmart2D()
    {
        $token = Sipay::getToken();

        $invoice_id = rand(10000000001, 99999999999);
        $merchant_key = config('payment.sipay.api_merchant_key');
        $app_secret = config('payment.sipay.app_secret');

        $hash = Sipay::generateHashKey(5.00,1,'TRY',$merchant_key,$invoice_id,$app_secret);

        $items = [["name" => "Item3","price" => 5.00,"quantity" => 1,"description" =>"item3 description"]];


        $inputs = [
            'cc_holder_name' => 'John Dao',
            'cc_no' => 5406675406675403,
            'expiry_month' => 12,
            'expiry_year' => 2026,
            'cvv' => '000',
            'currency_code' => 'TRY',
            'installments_number' => 1,
            'invoice_id' => $invoice_id,
            'invoice_description' => 'INVOICE TEST DESCRIPTION',
            'total' => 5.00,
            'merchant_key' => $merchant_key,
            'items' => $items,
            'name' => 'John',
            'surname' => 'Dao',
            'hash_key' => $hash,
        ];

        $paySmart2D = Sipay::paySmart2D($token->token, $inputs);
        echo $paySmart2D;
    }

    // Return Url - Success
    public function success(Request $request)
    {
        if($request->input('sipay_status') == 1){
            Cookie::queue(Cookie::forget('shopping_cart'));

            Log::debug('PAYMENT_3D_SUCCESS', [$request->all()]);

            $invoice = Bill::query()->find($request->input('invoice_id'));

            if($invoice){
                $invoice->order->update([
                    'status' => Order::STATUS_PAYMENT_SUCCESS,
                    'payment_order_no' => $request->input('order_no')
                ]);
            }

            return redirect()
                ->route('index')
                ->with('data', $request->all())
                ->with('success_message', 'Payment Success..');
        }

        return redirect()
            ->route('payment.index')
            ->with('data', $request->all())
            ->with('error_message', 'Payment Error..');

    }

    // Cancel Url - Fail
    public function fail(Request $request)
    {

        Log::debug('PAYMENT_3D_ERROR', [$request->all()]);

        return redirect()
            ->route('payment.index')
            ->with('data', $request->all())
            ->with('error_message', 'Payment Error..');
    }

    // Generate Hash Code
    public function hash($invoice_id)
    {
        $hash = Sipay::generateHashKey(5.00,1,'TRY','$2y$10$HmRgYosneqcwHj.UH7upGuyCZqpQ1ITgSMj9Vvxn.t6f.Vdf2SQFO',$invoice_id,'b46a67571aa1e7ef5641dc3fa6f1712a');

        echo $hash;
    }

    // Get Token
    public function token()
    {
        $token = Sipay::getToken();

        echo $token->token;
    }


    // Log Create
    public function createLog($data){
        Log::debug('TEST_LOG', [$data]);

        echo "Log Created";
    }
}
