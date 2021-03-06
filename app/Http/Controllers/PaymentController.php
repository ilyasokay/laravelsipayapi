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

        // Kart kaydetme seçilmişse bu kısımm çalışıyor.
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
//dd($saveCard);
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

        $invoice->id = $invoice->id + time() + time() + rand(1000,9999) +1;
        $invoice->save();

//dd($invoice);
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
            //$inputs['return_url'] = 'https://www.google.com/';
            $inputs['cancel_url'] = route('fail');
            //$inputs['cancel_url'] = 'https://laravel.com';

/*
$inputs1 = [
  "cc_holder_name" => null,
  "cc_no" => null,
  "expiry_month" => null,
  "expiry_year" => null,
  "cvv" => null,
  "currency_code" => "TRY",
  "installments_number" => 1,
  "invoice_id" => 26220406981,
  "invoice_description" => "Incoice description 3866",
  "total" => "65.00",
  "merchant_key" => "$2y$10$0X.RKmBNjKHg7vfJ8N46j.Zq.AU6vBVASro7AGGkaffB4mrdaV4mO",
  "name" => "",
  "surname" => "",
  "hash_key" => "65912bd6a1c42ce3:d166:qFFuRWQeTfjj6VZ__jH+__U7x5ESzE+7RwCVsWBAjdIZZsVqIqhydHgVEGlwyc2wY0VKHkAVjpozkS6dTmAVUeDhW1DmvOPoBX5IEvC0Y977cueXfXwcjlCkEknRSeSIXA",
  "items" => json_encode([["name" => "non eos autem","price" => "65.00","quantity" => 1,"description" =>"est expedita tempore porro et cum"]], JSON_UNESCAPED_UNICODE),
  "card_token" => "3TRLZ2VJUYQV6TW6JZMPYGJZRPH324VI7U6FRBSFP7FTENZ6",
  "customer_number" => 50,
  "customer_email" => "phauck@example.com",
  "customer_phone" => "5724635373",
  "customer_name" => "Donna Altenwerth",
  "return_url" => "https://www.google.com/",
  "cancel_url" => "https://laravel.com",
];
*/

            //dd($inputs);

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

        if($paySmart2D->status() == 200){

            $object = $paySmart2D->object();

            if($object->status_code != 100){
                return redirect()
                    ->route('payment.index')
                    ->with('data', $object)
                    ->with('error_message', $object->status_description. " - Status Code: ". $object->status_code);
            }

            Cookie::queue(Cookie::forget('shopping_cart'));

            $invoice = Bill::query()->find($object->data->invoice_id);
            if($invoice){
                $invoice->order->update([
                    'status' => Order::STATUS_PAYMENT_SUCCESS,
                    'payment_order_no' => $object->data->order_id
                ]);
            }

            return redirect()
                ->route('index')
                ->with('data', $object)
                ->with('success_message', 'Payment Success..');
        }

        return redirect()
            ->route('payment.index')
            ->with('data', $paySmart2D->object())
            ->with('error_message', 'Payment Error, Return data NULL');
    }
}
