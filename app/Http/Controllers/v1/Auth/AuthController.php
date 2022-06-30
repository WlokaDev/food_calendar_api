<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\v1\Controller;
use App\Http\Requests\EmailVerificationRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'unique:users,email'],
            'password' => 'required|string|confirmed|min:6'
        ]);

        try {
            $user = DB::transaction(static function () use ($data) {
                $user = User::query()->create([
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'])
                ]);

                // TODO Dodać zgody

                return $user;
            }, 3);
        } catch (\Exception $exception) {
            reportError($exception);

            return $this->errorResponse(
                __('messages.Something went wrong'),
                status: 500
            );
        }

        event(new Registered($user));


        return $this->successResponse(
            __('messages.To activate your account, click on the link sent to the e-mail address provided and log in')
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function checkEmailIsAvailable(Request $request) : JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email']
        ]);

        if(User::query()->where('email', $data['email'])->exists()) {
            return $this->successResponse(
                false
            );
        }

        return $this->successResponse(
            true
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function login(Request $request) : JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_firebase_token' => ['nullable', 'string']
        ]);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ])) {
            if (!Auth::user()->email_verified_at) {
                return $this->errorResponse(
                    __('messages.Email is not verified'),
                    status: 422
                );
            }

            if (isset($credentials['device_firebase_token'])) {
                Auth::user()->update([
                    'device_firebase_token' => $credentials['device_firebase_token']
                ]);
            }

            return $this->successResponse([
                'user' => Auth::user(),
                'access_token' => Auth::user()->createToken('auth')->plainTextToken
            ]);
        }

        return $this->errorResponse(
            __('messages.Credentials not valid'),
            status: 422
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email']
        ]);

        $status = Password::sendResetLink([
            'email' => $data['email']
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse([
                'message' => __('messages.Link for reset password has been sent on email.')
            ]);
        }

        return $this->errorResponse(
            $status,
            status: 422
        );
    }

    /***
     * @param Request $request
     * @return array
     */

    public function resetPassword(Request $request): array
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $status = Password::reset($data, static function (User $user, $password) {
            $user->password = Hash::make($password);
            $user->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(
                $status
            );
        }

        return $this->errorResponse(
            $status,
            status: 422
        );
    }

    /**
     * @param EmailVerificationRequest $request
     * @param Agent $agent
     * @return Redirect
     */

    public function verifyEmail(EmailVerificationRequest $request, Agent $agent): Redirect
    {
        $request->fulfill();

        return redirect()->to(match ($agent->platform()) {
            'AndroidOS' => 'food_calendar://food_calendar',
            'iOS' => 'com.food_calendar.food_calendar',
            default => 'https://food-calendar.pl'
        });
    }
}
