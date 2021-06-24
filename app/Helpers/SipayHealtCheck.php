<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SipayHealtCheck
{
    public static function getToken($appId = null, $appSecret = null, $appUrl = null)
    {
        $url = $appUrl;
        $config = [
            'app_id' => $appId,
            'app_secret' => $appSecret
        ];

        $request = Http::post($url,$config);

        return $request;
    }
}
