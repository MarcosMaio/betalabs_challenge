<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'The email is mandatory and must exist in the system.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified.'], 403);
        }

        $token = Password::getRepository()->create($user);

        $user->notify(new ResetPasswordNotification($token, $user->email));

        return response()->json([
            'message' => 'Password reset link sent to your email.',
        ], 200);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password reset successfully.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'An error occurred while resetting the password.',
                'error' => __($status),
            ], 500);
        }
    }
}
