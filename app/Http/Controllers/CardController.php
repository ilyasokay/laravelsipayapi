<?php

namespace App\Http\Controllers;

use App\Helpers\Sipay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CardController extends Controller
{
    // Get all save cards
    // Şu an çalışmıyor, bunun yerine "getCardTokens" kullanılıyor.
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
            'customer_number' => 50,
        ];
        $getSaveCards = Sipay::getSaveCards($token->token, $inputs);

        foreach ($getSaveCards as $key => $card){
            $cardInfo = Http::get('https://lookup.binlist.net/'.$card["bin"])->object();
            $getSaveCards[$key]["binlist"] = $cardInfo;
        }

        return response()->json(["data" => $getSaveCards ?? [] ]);
    }

    public function getCardTokens()
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
            'customer_number' => 797,
        ];
        $getCardTokens = Sipay::getCardTokens($token->token, $inputs);

        if(is_null($getCardTokens)){
            return response()->json([
                "status_code" => 0,
                'message' => 'Not working'
            ]);
        }

        $data = array_merge(["status" => $getCardTokens->status_code == 100 ? 'success' : 'error' ], (array)$getCardTokens);

        return response()->json($data);
    }

    // Create Save Card
    public function saveCard()
    {
        $user = User::find(auth()->user()->id);

        $getToken = Sipay::getToken();

        $hashKey = Sipay::generateSaveCardCreateHashKey(
            config('payment.sipay.api_merchant_key'),
            $user->customer_number,
            4543147422801147,
            'Aigerim ISBANK',
            '01',
            '2025',
            config('payment.sipay.app_secret')
        );

        $inputs = [
            'merchant_key' => config('payment.sipay.api_merchant_key'),
            'card_holder_name' => 'Aigerim ISBANK',
            'card_number' => 4543147422801147,
            'expiry_month' => '01',
            'expiry_year' => '2025',
            'customer_number' => $user->customer_number,
            'hash_key' => $hashKey,
            'customer_name' => 'Aigerim ISBANK',
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
    public function editCard(Request $request, $card_token)
    {
        $user = User::find(auth()->user()->id);

        $getToken = Sipay::getToken();

        $hashKey = Sipay::generateSaveCardEditOrDeleteHashKey(
            config('payment.sipay.api_merchant_key'),
            $user->customer_number,
            $card_token,
            config('payment.sipay.app_secret')
        );

        $inputs = [
            'merchant_key' => config('payment.sipay.api_merchant_key'),
            'card_token' => $card_token,
            'customer_number' => $user->customer_number,
            'expiry_month' => '12',
            'expiry_year' => '2026',
            'hash_key' => $hashKey,
            'card_holder_name' => $request->input('card_holder_name'),
        ];

        $editCard  = Sipay::editCard($getToken->token, $inputs);

        if($editCard){

            return response()->json(
                array_merge(["status" => $editCard->status_code == 100 ? "success" : "error"], (array)$editCard)
            );
        }

        return response()->json([
            "status" => "error",
            "message" => "Save card edit not successful",
            "data" => null,
        ]);
    }

    // Edit Save Card
    public function deleteCard(Request $request, $card_token)
    {
        $user = User::find(auth()->user()->id);

        $getToken = Sipay::getToken();

        $hashKey = Sipay::generateSaveCardEditOrDeleteHashKey(
            config('payment.sipay.api_merchant_key'),
            $user->customer_number,
            $card_token,
            config('payment.sipay.app_secret')
        );

        $inputs = [
            'merchant_key' => config('payment.sipay.api_merchant_key'),
            'card_token' => $card_token,
            'customer_number' => $user->customer_number,
            'hash_key' => $hashKey,
        ];

        $deleteCard  = Sipay::deleteCard($getToken->token, $inputs);

        if($deleteCard){

            return response()->json(
                array_merge(["status" => $deleteCard->status_code == 100 ? "success" : "error"], (array)$deleteCard)
            );
        }

        return response()->json([
            "status" => "error",
            "message" => "Save card delete not successful",
            "data" => null,
        ]);
    }

}
