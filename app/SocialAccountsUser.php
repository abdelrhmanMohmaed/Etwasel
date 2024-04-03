<?php

namespace App;

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ProviderUser;



class SocialAccountsUser
{
    public function findOrCreate(ProviderUser $providerUser, $provider)
    {
        // dd($providerUser);

        $account = SocialAccount::where('provider_id', $providerUser->getId())
            ->where('provider_name', $provider)->first();

        if ($account) {
            return $account->user;
        } else {
            $user = User::where('email', $providerUser->getEmail())->first();
            if (!$user) {
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'f_name' => $providerUser['given_name'] ?? NULL,
                    's_name' => $providerUser['family_name'] ?? NULL,
                    'avatar' => $providerUser->avatar,
                    'photo' => $providerUser->avatar,
                    'type' => 'user',
                ]);
            }
            $user->accounts()->create([
                'provider_name' => $provider,
                'provider_id' => $providerUser->getId(),
            ]);
            return $user;
        }
    }
}
