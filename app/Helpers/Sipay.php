<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Sipay
{
    public static function baseUrl()
    {
        if(session('sp_base_url'))
        {
            return session('sp_base_url');
        }

        return config('payment.sipay.api_url');
    }

    // Get Token
    public static function getToken($appId = null, $appSecret = null, $appUrl = null)
    {
/*
        if(session('sp_token'))
        {
            $expires_at = Carbon::create(session('sp_token')->expires_at);
            if(!$expires_at->subMinutes(10)->lt(now())){
                return session('sp_token');
            }
        }
*/
        $url = $appUrl ?? config('payment.sipay.api_url') . "/api/token";
        $config = [
            'app_id' => $appId ?? config('payment.sipay.app_key'),
            'app_secret' => $appSecret ?? config('payment.sipay.app_secret')
        ];

        $request = Http::post($url,$config)->object();

        if($request->status_code == 100){
            session()->put('sp_token', $request->data);
            return $request->data;
        }

        return null;
    }

    public static function getInstallments($token)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])
            ->post(config('payment.sipay.api_url') . "/api/installments", [
                'merchant_key' => config('payment.sipay.api_merchant_key')
            ])
            ->object();

        if(@$request->status_code == 100)
        {
            return $request->installments;
        }

        return null;
    }

    // Get Installment
    public static function getPos($token, $inputs = [])
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])
        ->post(config('payment.sipay.api_url') . "/api/getpos",$inputs)
        ->object();

        if(@$request->status_code == 100)
        {
            return $request->data;
        }

        return null;
    }

    // Get Commissions
    public static function getCommissions($token, $inputs = [])
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])
        ->get(config('payment.sipay.api_url') . "/api/commissions",$inputs)
        ->object();

        if(@$request->status_code == 100)
        {
            return $request->data;
        }

        return null;
    }

    // 3D Payment
    public static function paySmart3D($inputs = [])
    {
        $request = Http::post(config('payment.sipay.api_url') . "/api/paySmart3D",$inputs);

        return $request;
    }

    // Non 3D Payment
    public static function paySmart2D($token, $inputs = [])
    {
        $headers = [
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ];

        //$url = config('payment.sipay.api_url') . "/api/paySmart2D";
        $url = "https://laravelsipayapi.test/api/postdata";

        $request = Http::withHeaders($headers)
            ->withoutVerifying()
        ->post($url, $inputs);

        return $request->object();

       // dd($request->body());

        if($request->status() == 200){
            $object = $request->object();

            if($object->status_code == 100)
            {
                Log::debug('PAYMENT_2D_SUCCESS', [$object]);

            }else{
                Log::debug('PAYMENT_2D_ERROR', [$object]);
            }
        }

        Log::debug('PAYMENT_2D_ERROR', [$request->object()]);

        return $request;
    }

    // Pay By Card Token
    public static function payByCardToken($inputs = [])
    {
        $request = Http::withHeaders([
            'Accept' => 'application/json'
        ])
        ->post(config('payment.sipay.api_url') . "/api/payByCardToken",$inputs);
//dd($request->body());
        return $request->body();
    }

    // Check Status
    public static function checkStatus($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
        ->post(config('payment.sipay.api_url') . "/api/checkstatus",$inputs)
        ->object();

        return $request;
    }

    // Status
    public static function getStatus($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
            ->post(config('payment.sipay.api_url') . "/purchase/status",$inputs)
            ->object();

        return $request;
    }

    // Refund
    public static function refund($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
            ->post(config('payment.sipay.api_url') . "/api/refund",$inputs)
            ->object();

        return $request;
    }

    // Get Transaction

    public static function getTransactions($token, $inputs = [])
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
            ->post(config('payment.sipay.api_url') . "/api/getTransactions",$inputs);

        return $request;
    }

    // Get Save Cards
    public static function getSaveCards($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
        ->post(config('payment.sipay.api_url') . "/api/getSaveCards",$inputs);

        if($request->status() == 200){
            if(@$request->object()->status_code == 100)
            {
                return $request->object()->data;
            }
        }elseif ($request->status() == 404){
            $fakeSaveCardList = [
                [
                    "type" => 'Fake Card Info',
                    "id" => 11,
                    "pos_id" => 0,
                    "card_token" => "KELUUKPQBCGYLV7FP7I7VUQNZP7N5UX7JUCPKZVOPKEMCEQP",
                    "merchant_id" => 98950,
                    "customer_number" => "1122343443-98950",
                    "customer_name" => "Taygun Alban",
                    "customer_email" => "taigun@gmail.com",
                    "customer_phone" => "+8801749452019",
                    "bin" => "540668",
                    "created_at" => "2021-04-08T14:07:42.000000Z",
                    "updated_at" => "2021-04-08T14:07:42.000000Z"
                ]
            ];
            return $fakeSaveCardList;
        }

        return null;
    }

    // Get Card Tokens
    public static function getCardTokens($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
        ->get(config('payment.sipay.api_url') . "/api/getCardTokens",$inputs);

        if($request->status() == 200){
            $object = $request->object();

            if($object->status_code == 100){
                Log::debug('GET_CARD_TOKENS_SUCCESS', [$object]);
                return $object;
            }

            Log::debug('GET_CARD_TOKENS_ERROR', [$object]);
            return $object;

        }

        Log::debug('GET_CARD_TOKENS_ERROR', [$request->object()]);
        return null;

    }

    // Create Save Card
    public static function saveCard($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
        ->post(config('payment.sipay.api_url') . "/api/saveCard",$inputs);

        if($request->status() == 200){
            $object = $request->object();
            if($object->status_code == 100){

                Log::debug('SAVE_CARD_CREATE_SUCCESS', [$object]);

                return $object;
            }
            Log::debug('SAVE_CARD_CREATE_ERROR', [$object]);

            return $object;
        }

        Log::debug('SAVE_CARD_CREATE_ERROR', [$request->body()]);

        return null;
    }

    // Edit Save Card
    public static function editCard($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
            ->post(config('payment.sipay.api_url') . "/api/editCard",$inputs);

        if($request->status() == 200){
            $object = $request->object();
            if($object->status_code == 100){

                Log::debug('SAVE_CARD_EDIT_SUCCESS', [$object]);

                return $object;
            }
            Log::debug('SAVE_CARD_EDIT_ERROR', [$object]);

            return $object;
        }

        Log::debug('SAVE_CARD_EDIT_ERROR', [$request->body()]);

        return null;
    }

    // Delete Save Card
    public static function deleteCard($token, $inputs)
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
            ->post(config('payment.sipay.api_url') . "/api/deleteCard",$inputs);

        if($request->status() == 200){
            $object = $request->object();
            if($object->status_code == 100){

                Log::debug('SAVE_CARD_DELETE_SUCCESS', [$object]);

                return $object;
            }
            Log::debug('SAVE_CARD_DELETE_ERROR', [$object]);

            return $object;
        }

        Log::debug('SAVE_CARD_DELETE_ERROR', [$request->body()]);

        return null;
    }

    // Generate Create Savecard Hash Key
    public static function generateSaveCardCreateHashKey(
        $merchant_key,
        $customer_number,
        $card_number,
        $card_holder_name,
        $expiry_month,
        $expiry_year,
        $app_secret
    ){
        $data = $merchant_key.'|'. $customer_number .'|'.$card_holder_name.'|'.$card_number.'|'.$expiry_month.'|'.$expiry_year;
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);
        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);

        return $msg_encrypted_bundle;

    }

    // Generate Edit or Delete Savecard Hash Key
    public static function generateSaveCardEditOrDeleteHashKey(
        $merchant_key,
        $customer_number,
        $card_token,
        $app_secret
    ){
        $data = $merchant_key.'|'.$customer_number.'|'.$card_token;
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);
        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);

        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);

        return $msg_encrypted_bundle;
    }

    public static function generateTransactionHashKey(
        $merchant_key,
        $date,
        $invoiceid = "",
        $currency_id = "",
        $paymentmethodid = "",
        $minamount = "",
        $maxamount = "",
        $transactionState = ""
    ){

        $data = $date.'|'.$invoiceid.'|'.$currency_id.'|'.$paymentmethodid.'|'.$minamount.'|'.$maxamount.'|'.$transactionState;

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($merchant_key);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt(
            "$data", 'aes-256-cbc', "$saltWithPassword", null, $iv
        );
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $hash_key = str_replace('/', '__', $msg_encrypted_bundle);

        return $hash_key;
    }



    // Hash Key
    public static function generateHashKey(
        $total,
        $installment,
        $currency_code,
        $merchant_key,
        $invoice_id,
        $app_secret){

        $data = $total.'|'.$installment.'|'.$currency_code.'|'.$merchant_key.'|'.$invoice_id;

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);

        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);

        return $msg_encrypted_bundle;
    }

    public static function generateHash($data, $pass){

        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($pass);

        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);

        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);

        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);

        return $msg_encrypted_bundle;
    }

}
