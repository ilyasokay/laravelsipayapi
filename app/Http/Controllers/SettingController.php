<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SettingController extends Controller
{
    public function index()
    {
        /*
        $api_config = [
          "api_url" => null,
          "merchant_id" => null,
          "merchant_key" => null,
          "app_key" => null,
          "app_secret" => null,
        ];

        if($cookie = Cookie::get('sipay_api_config')){
            $cookie_data = stripslashes($cookie);
            $api_config = json_decode($cookie_data, true);
        }
*/
        return view('setting.index');
    }

    public function update(Request $request)
    {
        $requestOnly = $request->only([
            'api_url',
            'merchant_id',
            'merchant_key',
            'app_key',
            'app_secret',
        ]);

        $minutes = 60 * 24 * 30;
        Cookie::queue(Cookie::make('sipay_api_config', json_encode($requestOnly), $minutes));

        return redirect()
            ->route('setting.index')
            ->with('success_message', 'Form Update Success..');
    }
}
