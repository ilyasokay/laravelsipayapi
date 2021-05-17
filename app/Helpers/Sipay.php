<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Sipay
{
    // Get Token
    public static function getToken()
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
        $request = Http::post(
            config('payment.sipay.api_url') . "/api/token",
            [
                'app_id' => config('payment.sipay.app_key'),
                'app_secret' => config('payment.sipay.app_secret')
            ]
        )->object();

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
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json'
        ])
        ->post(config('payment.sipay.api_url') . "/api/paySmart2D",$inputs)->object();

        if(@$request->status_code == 100)
        {
            Log::debug('PAYMENT_2D_SUCCESS', [$request->data]);
            return $request->data;
        }else{
            Log::debug('PAYMENT_2D_ERROR', [$request]);
        }

        return null;
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

                Log::debug('SAVE_CARD_SUCCESS', [$object]);

                return $object;
            }
            Log::debug('SAVE_CARD_ERROR', [$object]);

            return $object;
        }

        Log::debug('SAVE_CARD_ERROR', [$request->body()]);

        return null;
    }

    // Generate Savecard Hash Key
    public static function generateSaveCardHashKey(
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

}
