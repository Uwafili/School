<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'], true)) {
            abort(404);
        }

        if (!config("services.$provider.client_id") || !config("services.$provider.client_secret")) {
            return redirect()->route('login')->with('error', ucfirst($provider).' login is not configured yet.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        if (!in_array($provider, ['google', 'facebook'], true)) {
            abort(404);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'FoodStore user',
                    'password' => Str::random(40),
                    'email_verified_at' => now(),
                    'usertype' => 'user',
                ]
            );

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->with('error', 'We could not sign you in with '.ucfirst($provider).'. Please try again.');
        }
    }
}

