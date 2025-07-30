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
use App\Http\Requests\User\SaveUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk manajemen data pengguna.
 *
 * @author Fahmi Fauzi Rahman
 * @link https://github.com/ffrz
 * @license MIT
 *
 * @OA\Tag(
 *     name="Users",
 *     description="API untuk manajemen user"
 * )
 *
 */
class UserController extends Controller
{

    /**
     *
     * Menampilkan daftar user dengan dukungan filter, search, dan sorting.
     *
     * @OA\Get(
     *     path="/api/v1/users",
     *     operationId="getUsers",
     *     tags={"Users"},
     *     summary="List semua user",
     *     description="Ambil data semua user dengan filter, search dan sorting",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="filter[role]",
     *         in="query",
     *         description="Filter berdasarkan role user",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Filter berdasarkan status user (active / inactive)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"active", "inactive"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Pencarian berdasarkan nama atau email",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Kolom untuk sorting (name, email, created_at, role, active)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Arah sorting (asc atau desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Jumlah item per halaman",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Halaman yang dipilih, defaultnya halaman pertama.",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(response=200, description="Daftar user dalam bentuk paginasi")
     * )
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny');

        $this->validateSort($request, ['name', 'email', 'created_at', 'role', 'active']);
        [$orderBy, $orderType] = $this->getSortParams($request);

        $filter = $request->get('filter', []);

        $q = User::query();

        if (!empty($filter['role']) && $filter['role'] != 'all') {
            $q->where('role', '=', $filter['role']);
        }

        if (isset($filter['status']) && in_array($filter['status'], ['active', 'inactive'])) {
            $q->where('active', $filter['status'] === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $q->orderBy($orderBy, $orderType);

        $users = $q->paginate((int) $request->get('per_page', 10))
            ->withQueryString();

        return ApiResponse::success('Daftar pengguna berhasil diambil', $users);
    }

    /**
     * Menyimpan user baru ke dalam database.
     *
     * @OA\Post(
     *     path="/api/v1/users",
     *     operationId="createUser",
     *     tags={"Users"},
     *     summary="Buat user baru",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="role", type="string", example="admin|user"),
     *             @OA\Property(property="active", type="boolean", example="true")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User berhasil dibuat")
     * )
     */
    public function store(SaveUserRequest $request)
    {
        $this->authorize('create');

        $user = $this->saveUser(new User(), $request->validated());

        return ApiResponse::success('Pengguna baru berhasil dibuat', $user);
    }

    /**
     * Menampilkan detail user berdasarkan ID.
     *
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     operationId="getUserById",
     *     tags={"Users"},
     *     summary="Ambil detail user berdasarkan ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User data",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=404, description="User tidak ditemukan"),
     *
     * )
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);
        return ApiResponse::success('Detail pengguna', $user);
    }

    /**
     * Memperbarui data user berdasarkan ID.
     *
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     operationId="updateUser",
     *     tags={"Users"},
     *     summary="Update data user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email"},
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *             @OA\Property(property="password", type="string", example="newpass123"),
     *             @OA\Property(property="role", type="string", example="admin|user"),
     *             @OA\Property(property="active", type="boolean", example="true")
     *         )
     *     ),
     *     @OA\Response(response=200, description="User berhasil diupdate"),
     *     @OA\Response(response=404, description="User tidak ditemukan")
     * )
     */
    public function update(SaveUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $user = $this->saveUser($user, $request->validated());

        return ApiResponse::success('Pengguna telah diperbarui.', $user);
    }

    /**
     * Menghapus user berdasarkan ID.
     *
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     operationId="deleteUser",
     *     tags={"Users"},
     *     summary="Hapus user berdasarkan ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User berhasil dihapus"),
     *     @OA\Response(response=404, description="User tidak ditemukan")
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->delete();

        return ApiResponse::success('Pengguna telah dihapus');
    }

    /**
     * Simpan data user ke database.
     *
     * @param User $user
     * @param array $validated
     * @return User
     */
    private function saveUser(User $user, array $validated)
    {
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return $user;
    }
}
