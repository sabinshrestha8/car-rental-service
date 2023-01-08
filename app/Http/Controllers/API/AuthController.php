<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\{StoreUserRequest, CheckUserRequest, ResetPasswordRequest};

class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function signup(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        $createdUser = User::create($validatedData);

        if ($createdUser) {
            $createdUser->roles()->attach(Role::where('name', 'User')->first());
            return response()->json('User Created Sucessfully', Response::HTTP_CREATED);
        }

        return response()->json('Could not create user', Response::HTTP_BAD_REQUEST);
    }

    /**
     * Generate sanctum token on successful login
     */
    public function login(CheckUserRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken($request->email)->plainTextToken
        ]);
    }

    /**
     * Revoke token; only remove token that is used to perform logout (i.e. will not revoke all tokens)
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        //$request->user->tokens()->delete(); // use this to revoke all tokens (logout from all devices)
        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * Get authenticated user details
     */
    public function getAuthenticatedUser(Request $request)
    {
        return $request->user();
    }

    public function sendPasswordResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)], Response::HTTP_OK);
        } else {
            throw ValidationException::withMessages([
                'email' => __($status)
            ]);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], Response::HTTP_OK);
        } else {
            throw ValidationException::withMessages([
                'email' => __($status)
            ]);
        }
    }
}
