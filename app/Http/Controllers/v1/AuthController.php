<?php

namespace App\Http\Controllers\v1;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserPasswordForgotRequest;
use App\Http\Requests\UserPasswordResetRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Http\Services\UsersService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    private UsersService $userService;

    /**
     * AuthController constructor.
     * @param UsersService $userService
     */
    public function __construct(UsersService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param \App\Http\Requests\UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (!Auth::attempt($data)) {
            return $this->errorResponse(__('auth.failed'), status: ResponseAlias::HTTP_UNAUTHORIZED);
        }
        if (!Auth::user()->email_verified_at) {
            return $this->errorResponse(__('auth.needs_email_confirmation'), status: ResponseAlias::HTTP_UNAUTHORIZED);
        }

        return $this->successResponse([
            'user' => new UserResource(Auth::user()),
            'access_token' => Auth::user()->createToken('auth')->plainTextToken,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return $this->successResponse(
            __('messages.Tokens Revoked')
        );
    }

    /**
     * @param \App\Http\Requests\UserRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        try {
            $this->userService->assignData($data);
            return $this->successResponse(__('messages.register.success'));
        } catch (\Exception $e) {
            reportError($e);
            return $this->errorResponse(__('messages.Something went wrong.'), status: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(string $token): JsonResponse
    {
        $user = User::firstWhere('email', Crypt::decrypt($token));

        if (!$user) {
            return $this->errorResponse(__('messages.Link is not valid or has expired'));
        }

        $user->email_verified_at = Carbon::now();
        $user->save();
        return $this->successResponse(__('messages.Successfully verified your email'));
    }

    /**
     * @param \App\Http\Requests\UserPasswordForgotRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordForgot(UserPasswordForgotRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::firstWhere('email', $data['email']);
        if (!$user) {
            return $this->errorResponse(
                __('messages.Provided email not found.'),
                status: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->password_reset_token = Str::uuid();
        $user->password_reset_token_expires_at = now()->addHour();
        $user->save();


        #!TODO Notification system.

        return $this->successResponse(
            __('messages.Link for reset password has been send.')
        );
    }

    /**
     * @param \App\Http\Requests\UserPasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function passwordReset(UserPasswordResetRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::wherePasswordResetToken($data['email'], $data['token']);
        if (!$user) {
            return $this->errorResponse(
                __('messages.Provided token is not valid or is expired.'),
                status: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = $user->first();
        $user->password_reset_token_expires_at = null;
        $user->password_reset_token = null;
        $user->password = bcrypt($data['password']);
        $user->save();

        return $this->successResponse(
            __('messages.Password has been reset.')
        );
    }
}
