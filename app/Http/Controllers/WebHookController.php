<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebHookController extends Controller
{
    public function saleWebHook(Request $request)
    {
        Log::debug('SALE_WEB_HOOK', [$request->all()]);
    }

    public function recurringWebHook(Request $request)
    {
        Log::debug('RECURRING_WEB_HOOK', [$request->all()]);
    }
}
