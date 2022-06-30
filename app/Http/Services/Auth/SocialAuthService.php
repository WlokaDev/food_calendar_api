<?php

namespace App\Http\Services\Auth;

use App\Enums\SocialAuthProviderEnum;
use App\Http\Services\UsersService;
use App\Models\SocialAccount;
use App\Models\User;

class SocialAuthService
{
    /**
     * @var SocialAccount
     */

    protected SocialAccount $socialAccount;

    /**
     * @param SocialAccount|null $socialAccount
     */

    public function __construct(SocialAccount $socialAccount = null)
    {
        $this->socialAccount = $socialAccount ?: new SocialAccount();
    }

    /**
     * @param string $email
     * @param SocialAuthProviderEnum $socialAuthProviderName
     * @param string $socialAuthProviderId
     * @param User|null $user
     * @return $this
     */

    public function setAccount(
        string $email,
        SocialAuthProviderEnum $socialAuthProviderName,
        string $socialAuthProviderId,
        ?User $user = null
    ) : self
    {
        if(!$user) {
            $user = User::where('email', $email)->first();

            $userService = new UsersService($user);
            $user = $userService->assignData(
                $email,
                setVerified: true
            )->getUser();
        }

        $socialAccount = SocialAccount::query()
            ->where([
                'provider_name' => $socialAuthProviderName,
                'provider_id' => $socialAuthProviderId
            ])->first();

        if($socialAccount) {
            $this->setSocialAccount($socialAccount);
        } else {
            $this->socialAccount->user()->associate($user);
            $this->socialAccount->provider_id = $socialAuthProviderId;
            $this->socialAccount->provider_name = $socialAuthProviderName;
            $this->socialAccount->save();
        }

        return $this;
    }

    /**
     * @param SocialAccount $socialAccount
     */

    private function setSocialAccount(SocialAccount $socialAccount): void
    {
        $this->socialAccount = $socialAccount;
    }

    /**
     * @return SocialAccount
     */

    public function getSocialAccount(): SocialAccount
    {
        return $this->socialAccount;
    }

}
