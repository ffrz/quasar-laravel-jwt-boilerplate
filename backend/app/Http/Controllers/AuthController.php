<?php

/**
 * MIT License
 *
 * Copyright (c) 2025 Fahmi Fauzi Rahman <fahmifauzirahman@gmail.com> (https://github.com/ffrz)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\AclService;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Controller untuk otentikasi pengguna.
 *
 * @author Fahmi Fauzi Rahman
 * @link https://github.com/ffrz
 * @license MIT
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="API untuk register, login, dan cek user"
 * )
 *
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/register",
     *     tags={"Authentication"},
     *     summary="Register user baru",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User berhasil didaftarkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     *
     * Register a new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::Role_User,
            'active' => true,
        ]);

        // perlu kirim token kalau mau automatic login setelah register
        $token = $this->auth()->fromUser($user);

        return ApiResponse::success('Success', [
            ...$this->tokenResponse($token),
            ...$this->userResponse($user),
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     tags={"Authentication"},
     *     summary="Login dan dapatkan JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     *
     * Login user dan kembalikan token JWT
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials['active'] = true; // hanya user aktif yang bisa login

        if (!$token = $this->auth()->attempt($credentials)) {
            return ApiResponse::error('Email atau password salah', 401);
        }

        return ApiResponse::success('Login success', [
            ...$this->tokenResponse($token),
            ...$this->userResponse(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/me",
     *     tags={"Authentication"},
     *     summary="Get info user yang sedang login",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data user",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     *
     * Ambil data user yang sedang login
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return ApiResponse::success(
            'Success',
            $this->userResponse()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     tags={"Authentication"},
     *     summary="Logout user dan invalidate token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout berhasil"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     *
     * Logout user dan invalidate token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $this->auth()->logout();
            return ApiResponse::success('Logout berhasil');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ApiResponse::error('Gagal logout', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh JWT token",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token baru diberikan"
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function refresh()
    {
        try {
            return ApiResponse::success(
                'Token diperbarui',
                $this->tokenResponse($this->auth()->refresh())
            );
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ApiResponse::error('Token tidak valid', 401);
        }
    }

    /**
     * Buat response token JWT.
     *
     * @param string $token
     * @return array
     */
    private function tokenResponse($token): array
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->auth()->factory()->getTTL() * 60,
        ];
    }

    /**
     * Buat response informasi user dan permission ACL-nya.
     *
     * @return array
     */
    private function userResponse(?User $user = null): array
    {
        $user = $user ?? $this->auth()->user();
        return [
            'user' => new UserResource($user),
            'permissions' => app(AclService::class)->getUserPermissions($user),
        ];
    }
}
