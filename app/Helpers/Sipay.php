<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class Sipay
{
    // Get Token
    public static function getToken()
    {
        if(session('sp_token'))
        {
            $expires_at = Carbon::create(session('sp_token')->expires_at);
            if(!$expires_at->subMinutes(10)->lt(now())){
                return session('sp_token');
            }
        }

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

    // Get Installment
    public static function getInstallment($token, $inputs = [])
    {
        $request = Http::withHeaders([
            'Authorization' => 'Bearer '. $token,
            'Accept' => 'application/json',
        ])
        ->post(config('payment.sipay.api_url') . "/api/getpos",$inputs)
        ->object();

        if($request->status_code == 100)
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

        if($request->status_code == 100)
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
        ])
        ->post(config('payment.sipay.api_url') . "/api/paySmart2D",$inputs)
        ->object();

        if($request->status_code == 100)
        {
            return $request->data;
        }

        return null;
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
