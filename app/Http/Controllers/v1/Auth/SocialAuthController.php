<?php

namespace App\Http\Controllers\v1\Auth;

use App\Enums\SocialAuthProviderEnum;
use App\Http\Controllers\v1\Controller;
use App\Http\Services\Auth\SocialAuthProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Laravel\Socialite\Facades\Socialite;
use Jenssegers\Agent\Agent;

class SocialAuthController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */

    public function auth(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        $data = $request->validate([
            'provider' => [
                'required',
                'string',
                new Enum(SocialAuthProviderEnum::class)
            ],
        ]);

        return Socialite::driver($data['provider'])->redirect();
    }

    /**
     * @param Request $request
     * @param SocialAuthProvider $socialAuthProvider
     * @param Agent $agent
     * @return JsonResponse|RedirectResponse
     */

    public function callback(Request $request, SocialAuthProvider $socialAuthProvider, Agent $agent): JsonResponse|RedirectResponse
    {
        try {
            $user = $socialAuthProvider->callback(
                SocialAuthProviderEnum::tryFrom(
                    $request->resolver
                )
            )->user;
        } catch (\Exception $e) {
            reportError($e);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        return redirect()->to(match ($agent->platform()) {
            'AndroidOS' => 'food_calendar://food_calendar?token=' . urlencode($user->createToken('auth')->plainTextToken),
            'iOS' => 'com.food_calendar.food_calendar://token=' . urlencode($user->createToken('auth')->plainTextToken),
            default => 'https://food-calendar.pl'
        });
    }
}
