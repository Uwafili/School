<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google\Auth\OAuth2;
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

            // Verify the token with Google
            $client = new \Google_Client([
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            ]);

            $payload = $client->verifyIdToken($token);

            if (!$payload) {
                return back()->with('error', 'Invalid Google token');
            }

            // Token is valid, get user info
            $name = $payload['name'] ?? '';
            $email = $payload['email'] ?? '';
            $picture = $payload['picture'] ?? null;

            // Find or create user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(str_random(16)), // Random password
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

