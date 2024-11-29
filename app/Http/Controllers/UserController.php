<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();
        $is_admin = $user->is_admin;

        if ($request->all() === []) {
            return response()->json([
                'error' => 'Please provide at least one field to update',
            ], 422);
        }

        if ($request->has('is_admin') && !$is_admin) {
            return response()->json([
                'error' => 'You do not have permission to update this field',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|min:3|max:255',
            'email' => 'sometimes|email|min:3|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        if (isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }
        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }
        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        if ($is_admin && $request->has('is_admin')) {
            $user->is_admin = $request->is_admin;
        }

        $user->updated_at = now();
        $user->save();

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->only(['name', 'email', 'created_at', 'updated_at']),
        ], 201);
    }

    public function show(Request $request)
    {
        $currentUserId = $request->user()->id;

        $users = User::where('id','!=', $currentUserId)->get();

        return UserResource::collection($users);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 204);
    }

}
