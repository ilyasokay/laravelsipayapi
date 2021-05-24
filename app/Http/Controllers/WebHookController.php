<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebHookController extends Controller
{
    public function saleWebHook(Request $request)
    {
        Mail::send('email.hello',["data" => $request->all()], function($message){
            $message->from('info@example.com', 'İletişim');
            $message->subject("SALE_WEB_HOOK");
            $message->to("info@example");
        });

        Log::debug('SALE_WEB_HOOK', [$request->all()]);
    }

    public function recurringWebHook(Request $request)
    {
        Mail::send('email.hello',["data" => $request->all()], function($message){
            $message->from('info@example.com', 'İletişim');
            $message->subject("RECURRING_WEB_HOOK");
            $message->to("info@example");
        });

        Log::debug('RECURRING_WEB_HOOK', [$request->all()]);
    }
}
