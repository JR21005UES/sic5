<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        Log::info('User created', ['user' => $user]);

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('Token created', ['token' => $token]);

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer'], 200);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json('Error de credenciales', 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()
            ->json(['data' => $user, 'access_token' => $token, 'token_type' => "Bearer"], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()
            ->json(['message' => 'Tokens Revoked'], 200);
    }
}