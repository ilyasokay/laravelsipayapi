<?php

namespace App\Http\Controllers;

use App\Helpers\Sipay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CardController extends Controller
{
    // Get all save cards
    public function getSaveCards()
    {
        $token = Sipay::getToken();
        $user = User::find(@auth()->user()->id);
        if(!$user){
            return response()->json([
                "status_code" => 401,
                'message' => 'User Not Login'
            ]);
        }

        if(!$token){
            return response()->json([
                "status_code" => 401,
                'message' => 'Unauthorized'
            ]);
        }

        $inputs = [
            'merchant_key' => config('payment.sipay.api_merchant_key'),
            'customer_number' => $user->customer_number,
        ];
        $getSaveCards = Sipay::getSaveCards($token->token, $inputs);

        foreach ($getSaveCards as $key => $card){
            $cardInfo = Http::get('https://lookup.binlist.net/'.$card["bin"])->object();
            $getSaveCards[$key]["binlist"] = $cardInfo;
        }

        return response()->json(["data" => $getSaveCards ?? [] ]);
    }

    // Create Save Card
    public function saveCard()
    {
        $user = User::find(auth()->user()->id);

        $getToken = Sipay::getToken();

        $hashKey = Sipay::generateSaveCardHashKey(
            config('payment.sipay.api_merchant_key'),
            $user->customer_number,
            5406675406675403,
            'Test Ali UZUN',
            '12',
            '2026',
            config('payment.sipay.app_secret')
        );

        $inputs = [
            'merchant_key' => config('payment.sipay.api_merchant_key'),
            'card_holder_name' => 'Test Ali UZUN',
            'card_number' => 5406675406675403,
            'expiry_month' => '12',
            'expiry_year' => '2026',
            'customer_number' => $user->customer_number,
            'hash_key' => $hashKey,
            'customer_name' => 'Test Ali UZUN',
            'customer_phone' => $user->phone,
        ];

        $saveCard  = Sipay::saveCard($getToken->token, $inputs);

        if($saveCard){

            return response()->json(
                array_merge(["status" => $saveCard->status_code == 100 ? "success" : "error"], (array)$saveCard)
            );
        }

        return response()->json([
            "status" => "error",
            "message" => "Save card insertion not successful",
            "data" => null,
        ]);
    }

    // Edit Save Card
    public function editCard($card_token)
    {
        $user = User::find(auth()->user()->id);

        $getToken = Sipay::getToken();

        $hashKey = Sipay::generateSaveCardHashKey(
            config('payment.sipay.api_merchant_key'),
            $user->customer_number,
            5406675406675403,
            'Test Ali UZUN',
            '12',
            '2026',
            config('payment.sipay.app_secret')
        );

        $inputs = [
            'merchant_key' => config('payment.sipay.api_merchant_key'),
            'card_token' => $card_token,
            'customer_number' => $user->customer_number,
            'expiry_month' => '12',
            'expiry_year' => '2026',
            'hash_key' => $hashKey,
            'card_holder_name' => 'Test Ali UZUN',
        ];
    }

}
