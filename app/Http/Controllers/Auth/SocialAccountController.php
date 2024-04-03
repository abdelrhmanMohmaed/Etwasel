<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\SocialAccountsUser;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAccountController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    public function handleProviderCallback(SocialAccountsUser $profileService, $provider)
    {

        try {
            $user = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->to('login');
        }

        $authUser = $profileService->findOrCreate($user, $provider);


        auth()->login($authUser, 'true');
        return redirect()->to('home');
    }
}
