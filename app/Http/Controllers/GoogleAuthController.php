<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
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

            // Decode the JWT token (without verification first - just to get email)
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return back()->with('error', 'Invalid token format');
            }

            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (!$payload || !isset($payload['email'])) {
                return back()->with('error', 'Invalid token payload');
            }

            $email = $payload['email'];
            $name = $payload['name'] ?? 'User';

            // Find or create user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(uniqid(rand(), true)),
                    'usertype' => 'user',
                ]);
            }

            // Log the user in
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Logged in with Google!');
        } catch (Exception $e) {
            return back()->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}

