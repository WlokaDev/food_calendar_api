<?php

namespace App\Http\Services\Auth;

use App\Enums\SocialAuthProviderEnum;
use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

final class SocialAuthProvider
{
    /**
     * @var SocialAuthService
     */

    protected SocialAuthService $socialAuthService;


    /**
     * @param SocialAuthProviderEnum $socialAuthProvider
     * @return SocialAccount
     */

    public function callback(SocialAuthProviderEnum $socialAuthProvider) : SocialAccount
    {
        $userSocial = Socialite::driver(
            $socialAuthProvider->value
        )->user();

        $this->socialAuthService->setAccount(
            $userSocial->getEmail(),
            SocialAuthProviderEnum::FACEBOOK,
            $userSocial->getId(),
            $this->getUser(
                $userSocial->getEmail()
            )
        );

        return $this->socialAuthService->getSocialAccount();
    }

    /**
     * @param string $email
     * @return User|null
     */

    public function getUser(string $email) : ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }
}
