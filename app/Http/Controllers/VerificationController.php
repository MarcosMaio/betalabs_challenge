<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $userId = $request->route('id');

        $user = User::findOrFail($userId);

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully.'], 200);
    }
}
