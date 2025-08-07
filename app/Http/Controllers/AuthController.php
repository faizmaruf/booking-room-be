<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Traits\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class AuthController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        //
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi berupa NIP.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            $errorMessages = collect($validator->errors()->all())->implode(', ');
            return $this->formatResponse(422, $errorMessages, null);
        }

        $credentials = $validator->validated();
        $email = $credentials['email'];
        $password = $credentials['password'];

        // Cek user berdasarkan email
        $user = DB::table('users')
            ->select('id', 'email', 'role', 'password')
            ->where('email', $email)
            ->first();

        if (!$user) {
            return $this->formatResponse(401, 'Unauthorized: NIP tidak ditemukan', null);
        }

        // Verifikasi password
        if (!Hash::check($password, $user->password)) {
            return $this->formatResponse(401, 'NIP atau Password salah', null);
        }

        // Buat token
        $issuedAt = Carbon::now()->timestamp;
        $expiration = Carbon::now()->addHours(24)->timestamp;

        $payload = [
            'iat'  => $issuedAt,
            'exp'  => $expiration,
            'id'  => $user->id,
            'email' => $user->email,
            'role'  => $user->role,
        ];

        $secretKey = env('JWT_SECRET');
        if (!$secretKey) {
            return $this->formatResponse(500, 'JWT_SECRET belum diatur di .env', null);
        }

        $token = JWT::encode($payload, $secretKey, 'HS256');

        return $this->formatResponse(200, 'Login Berhasil', [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $expiration,
        ]);
    }
    public function me()
    {
        $data =  Auth::user();
        return $this->formatResponse(200, 'success', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
