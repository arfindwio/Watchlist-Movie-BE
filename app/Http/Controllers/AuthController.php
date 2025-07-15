<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function editProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'photo' => 'sometimes|file|image|max:2048',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($user->photo && \Storage::disk('public')->exists('photos/' . $user->photo)) {
                \Storage::disk('public')->delete('photos/' . $user->photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('photos', 'public');
            $user->photo = basename($path);
        }

        $user->save();

        return UserResource::responseWithUser($user, 'Profile updated successfully');
    }

    public function deleteProfilePhoto(Request $request)
    {
        $user = $request->user();

        $photoPath = 'photos/' . $user->photo;

        if ($user->photo && \Storage::disk('public')->exists($photoPath)) {
            \Storage::disk('public')->delete($photoPath);
        }

        $user->photo = null;
        $user->save();

        return UserResource::responseWithUser($user, 'Profile photo deleted (if existed)');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return UserResource::responseLogout();
    }
}
