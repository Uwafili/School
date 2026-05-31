<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    // Redirect to Google
    public function googleLogin()
    {
        return Socialite::driver('google')
            ->scopes(['profile', 'email'])
            ->redirect();
    }

    // Handle Google callback
    public function googleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed.');
        }

        // Check if user exists, if not create
        $existingUser = User::where('email', $user->email)->first();

        if ($existingUser) {
            Auth::login($existingUser);
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt('google-auth-password'), // Dummy password
                'usertype' => 'user',
            ]);
            Auth::login($newUser);
        }

        return redirect()->route('dashboard')->with('success', 'Logged in with Google!');
    }
}
