<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserPresenceController extends Controller
{
    public function markOnline(Request $request)
    {
        Cache::put(
            'user-online-'.auth()->id(),
            true,
            now()->addMinutes(5)
        );

        return response()->json(['success' => true]);
    }

    public function markOffline(Request $request)
    {
        Cache::forget(
            'user-online-'.auth()->id()
        );

        return response()->json(['success' => true]);
    }
}
