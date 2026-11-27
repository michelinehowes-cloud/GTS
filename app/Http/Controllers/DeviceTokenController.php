<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The Android app registers its push-notification token here after the user logs in.
 */
class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => 'required|string|min:20|max:512',
            'platform' => 'nullable|in:android,ios',
        ]);

        DeviceToken::register($request->user(), $validated['token'], $validated['platform'] ?? 'android');

        // Remembered for this login session so the phone is unlinked on logout
        $request->session()->put('device_token', $validated['token']);

        return response()->json(['status' => 'success']);
    }
}
