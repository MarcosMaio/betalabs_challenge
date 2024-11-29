<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|min:3|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $user->sendEmailVerificationNotification();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => [
                    'Registration failed. Please try again.',
                    'exception' => $e->getMessage(),
                ]
            ], 500);
        }

        return response()->json([
            'message' => [
                'Registration successful. Please check your email.',
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => [
                    'The provided credentials are incorrect.',
                ]
            ], 422);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email not verified.'], 403);
        }

        if ($user->tokens()->where('tokenable_id', $user->id)->exists()) {
            return response()->json([
                'error' => [
                    'This user is already logged in.',
                ]
            ], 422);
        }

        $tokenResult = $user->createToken('auth_token', ['*']);
        $token = $tokenResult->plainTextToken;

        $tokenModel = PersonalAccessToken::findToken($token);

        if ($tokenModel) {
            $tokenModel->expires_at = now()->addHours(2);
            $tokenModel->save();
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
