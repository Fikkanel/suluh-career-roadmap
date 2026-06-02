<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException | \Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Email sudah terdaftar.',
                'errors'  => ['email' => ['Email ini sudah digunakan.']],
            ], 422);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $user = auth()->user();
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message'     => 'Login berhasil.',
            'user'        => $user,
            'token'       => $token,
            'token_type'  => 'bearer',
            'expires_in'  => config('jwt.ttl') * 60,
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Logout berhasil.']);
    }
}
