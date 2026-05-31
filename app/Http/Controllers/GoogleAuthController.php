<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Exception;

class GoogleAuthController extends Controller
{
    public function verify(Request $request)
    {
        try {
            $token = $request->input('token');
            
            if (!$token) {
                return back()->with('error', 'Google token not received');
            }

            // Decode the JWT token (just to get email/name - no DB storage)
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return back()->with('error', 'Invalid token format');
            }

            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (!$payload || !isset($payload['email'])) {
                return back()->with('error', 'Invalid token payload');
            }

            // Store in session only (NO database)
            Session::put('google_user', [
                'email' => $payload['email'],
                'name' => $payload['name'] ?? 'User',
                'picture' => $payload['picture'] ?? null,
            ]);

            Session::put('authenticated', true);

            return redirect()->route('dashboard')->with('success', 'Welcome!');
        } catch (Exception $e) {
            return back()->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Session::forget('google_user');
        Session::forget('authenticated');
        return redirect()->route('login')->with('success', 'Logged out');
    }
}

