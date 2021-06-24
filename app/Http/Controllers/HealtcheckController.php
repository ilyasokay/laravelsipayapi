<?php

namespace App\Http\Controllers;

use App\Helpers\SipayHealtCheck;
use Illuminate\Http\Request;

class HealtcheckController extends Controller
{
    public function index()
    {
        return view('healtcheck.index');
    }

    public function token(Request $request)
    {
        // Url
        $url = $request->input('api_base_url')."/api/token";
        $app_key = $request->input('app_key');
        $app_secret = $request->input('app_secret');

        $request = SipayHealtCheck::getToken($app_key, $app_secret, $url);

        if($request->status() == 200){
            $data = $request->object();

            if($data){
                $responseData = ["status" => "success", "dataType" => "json", "data" => $data];
                if($data->status_code != 100){
                    $responseData["status"] = "error";
                }
                return response()->json($responseData);
            }

            $responseData = ["status" => "success", "dataType" => "json", "data" => null];
            if($request->body()){
                $responseData = ["status" => "success", "dataType" => "html", "data" => $request->body()];
            }
            return response()->json($responseData);
        }

        $responseData = ["status" => "error", "dataType" => "html", "data" => "Not Found!"];
        return response()->json($responseData);
    }


}
