<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token tidak ditemukan di Authorization header'
            ], 401);
        }

        try {
            $secretKey = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Ambil user dari database berdasarkan UUID, ID, atau email dari payload
            // Sesuaikan dengan isi token kamu
            $userId = $decoded->id ?? null; // ganti sesuai key token kamu

            if (!$userId) {
                return response()->json(['message' => 'User ID tidak ditemukan di token'], 401);
            }

            $user = User::find($userId);


            if (!$user) {
                return response()->json(['message' => 'User tidak ditemukan'], 401);
            }

            // Set user ke Laravel Auth
            Auth::setUser($user);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Token tidak valid: ' . $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
