<?php

namespace App\Http\Controllers;

use App\Helpers\Sipay;
use App\Models\Bill;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PaymentController extends Controller
{
    public function index()
    {
        if(auth()->guest()){
            return redirect()->route('login');
        }

        $user = User::find(auth()->user()->id);

        if(!Cookie::get('shopping_cart')){
            return redirect()->route('index');
        }
        $cookie_data = stripslashes(Cookie::get('shopping_cart'));
        $cart_data = json_decode($cookie_data, true);

        $getToken = Sipay::getToken();
        $is_3d = is_null($getToken) ? 0 : $getToken->is_3d;

        return view('payment.index')
            ->with('user', $user)
            ->with('is_3d', $is_3d)
            ->with('cart_items', $cart_data);
    }

    // Payment
    public function store(Request $request)
    {

        if(! auth()->check()){
            return redirect()->route('index')
                ->with('error_message', 'Please login!');
        }

        $user = User::find(auth()->user()->id);
        $address = $user->address;
        $getToken = Sipay::getToken();

        // If the registered card is not selected, give an error.
        if($request->has('payment_save_card')){
            if($request->input('card_token') == null){

                return redirect()
                    ->route('payment.index')
                    ->with('warning_message', 'Please select a card.');
            }
        }

        if($request->input('save_my_card')){

            $saveCardHashKey = Sipay::generateSaveCardCreateHashKey(
                config('payment.sipay.api_merchant_key'),
                $user->customer_number,
                $request->input('credit_card'),
                $request->input('fullname'),
                $request->input('expiry_month'),
                $request->input('expiry_year'),
                config('payment.sipay.app_secret')
            );

            $inputs = [
                'merchant_key' => config('payment.sipay.api_merchant_key'),
                'card_holder_name' => $request->input('fullname'),
                'card_number' => $request->input('credit_card'),
                'expiry_month' => $request->input('expiry_month'),
                'expiry_year' => $request->input('expiry_year'),
                'customer_number' => $user->customer_number,
                'hash_key' => $saveCardHashKey,
                'customer_name' => $request->input('fullname'),
                'customer_phone' => $user->phone,
            ];

            $saveCard  = Sipay::saveCard($getToken->token, $inputs);

            if(@$saveCard->status_code != 100){
                return redirect()
                    ->route('payment.index')
                    ->with('data', $saveCard)
                    ->with('warning_message', 'Save card insertion not successful');
            }
        }

        $items = [];

        // Get Items
        $cookie_data = stripslashes(Cookie::get('shopping_cart'));
        $cart_data = json_decode($cookie_data, true);

        foreach ($cart_data as $key => $item){
            $product = Product::find($item['item_id']);
            $items[] = [
               "name" => $item['item_name'],
               "price" => $item['item_price'],
               "quantity" => (int)$item['item_quantity'],
               "description" => $product->description,
            ];
        }

       // $invoice_id = rand(10000000001, 99999999999);
        $merchant_key = config('payment.sipay.api_merchant_key');
        $app_secret = config('payment.sipay.app_secret');

        $order = Order::query()->create([
            'status' => Order::STATUS_PAYMENT_WAITING,
            'user_id' => $user->id,
            'address_id' => $address->id
        ]);

        $order->items()->createMany($items);

        $invoice = $order->invoice()->create([
            'description' => 'Incoice description '. rand(1000,9999),
            'bill_address1' => 'Address 1',
            'bill_address2' => 'Address 2',
            'bill_city' => 'İstanbul',
            'bill_postcode' => '3434',
            'bill_state' => 'Bağdat Caddesi',
            'bill_country' => 'Türkiye',
            'bill_email' => $user->email,
            'bill_phone' => '5370000000',
        ]);


        $hash = Sipay::generateHashKey($request->input('total'),$request->input('installments_number',1),'TRY',$merchant_key,$invoice->id,$app_secret);

        $fullname = explode(" ", $request->input('fullname'));
        $name = $fullname[0]; $surname = $fullname[count($fullname) - 1];

        $inputs = [
            'cc_holder_name' => $request->input('fullname'),
            'cc_no' => $request->input('credit_card'),
            'expiry_month' => $request->input('expiry_month'),
            'expiry_year' => $request->input('expiry_year'),
            'cvv' => $request->input('cvv'),
            'currency_code' => 'TRY',
            'installments_number' => $request->input('installments_number', 1),
            'invoice_id' => $invoice->id,
            'invoice_description' => $invoice->description,
            'total' => $request->input('total'),
            'merchant_key' => $merchant_key,
            'name' => $name,
            'surname' => $surname,
            'hash_key' => $hash,
            'items' => $items,
        ];

        // Kayıtlı Kart ile ödeme
        if($request->has('card_token') && $request->input('customer_number')){
            $inputs['card_token'] = $request->input('card_token');

            $inputs['customer_number'] = $request->input('customer_number');
            $inputs['customer_email'] = $request->input('customer_email');
            $inputs['customer_phone'] = $request->input('customer_phone');
            $inputs['customer_name'] = $user->name;
            $inputs['currency_code'] = $request->input('currency_code');

            $inputs['items'] = json_encode($inputs['items'], JSON_UNESCAPED_UNICODE);
            $inputs['return_url'] = route('success');
            $inputs['cancel_url'] = route('fail');

            $payByCardToken = Sipay::payByCardToken($inputs);

            return $payByCardToken;
        }

        if($request->has('is_3d') && $request->input('is_3d') == 1){
            $inputs['items'] = json_encode($inputs['items'], JSON_UNESCAPED_UNICODE);
            $inputs['return_url'] = route('success');
            $inputs['cancel_url'] = route('fail');

            $paySmart3D = Sipay::paySmart3D($inputs);

            return $paySmart3D->body();
        }

        $paySmart2D = Sipay::paySmart2D($getToken->token, $inputs);

        if(!is_null($paySmart2D)){

            if($paySmart2D->status_code != 100){
                return redirect()
                    ->route('payment.index')
                    ->with('data', $paySmart2D)
                    ->with('error_message', $paySmart2D->status_description. " - Status Code: ". $paySmart2D->status_code);
            }

            Cookie::queue(Cookie::forget('shopping_cart'));

            $invoice = Bill::query()->find($paySmart2D->data->invoice_id);
            if($invoice){
                $invoice->order->update([
                    'status' => Order::STATUS_PAYMENT_SUCCESS,
                    'payment_order_no' => $paySmart2D->data->order_id
                ]);
            }

            return redirect()
                ->route('index')
                ->with('data', $paySmart2D)
                ->with('success_message', 'Payment Success..');
        }

        return redirect()
            ->route('payment.index')
            ->with('error_message', 'Payment Error, Return data NULL');
    }
}
