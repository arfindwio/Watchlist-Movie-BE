<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return UserResource::responseWithUserAndToken($user, $token, 'User registered successfully', true, 201);
    }
    
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return UserResource::responseError('The credentials are incorrect.', false, 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return UserResource::responseWithUserAndToken($user, $token, 'Login successful');
    }

    public function authenticate(Request $request)
    {
        return UserResource::responseWithUser($request->user(), 'User retrieved');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return UserResource::responseLogout();
    }

}
