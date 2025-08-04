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
use App\Http\Requests\ProductCategory\SaveProductCategoryRequest;
use App\Http\Requests\User\SaveUserRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

/**
 * Controller untuk manajemen data kategori produk.
 *
 * @author Fahmi Fauzi Rahman
 * @link https://github.com/ffrz
 * @license MIT
 *
 * @OA\Tag(
 *     name="Product Categories",
 *     description="API untuk manajemen kategori produk"
 * )
 *
 */
class ProductCatgoryController extends Controller
{
    /**
     *
     * Menampilkan daftar user dengan dukungan filter, search, dan sorting.
     *
     * @OA\Get(
     *     path="/api/v1/product-categories",
     *     operationId="getProductCategories",
     *     tags={"ProductCategories"},
     *     summary="List semua kategori produk",
     *     description="Ambil data semua kategori produk dengan filter, search dan sorting",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Pencarian berdasarkan nama kategori atau deskripsi",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Kolom untuk sorting (name, description)",
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
     *     @OA\Response(response=200, description="Daftar kategori produk dalam bentuk paginasi")
     * )
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny');

        $this->validateSort($request, ['name', 'description']);
        [$orderBy, $orderType] = $this->getSortParams($request);

        $filter = $request->get('filter', []);

        $q = ProductCategory::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $q->orderBy($orderBy, $orderType);

        $users = $q->paginate((int) $request->get('per_page', 10))
            ->withQueryString();

        return ApiResponse::success('Daftar kategori produk berhasil diambil', $users);
    }

    /**
     * Menyimpan kategori produk baru ke dalam database.
     *
     * @OA\Post(
     *     path="/api/v1/product-categories",
     *     operationId="createProductCategory",
     *     tags={"ProductCategories"},
     *     summary="Buat kategori produk baru",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description"},
     *             @OA\Property(property="name", type="string", example="Aksesoris Laptop"),
     *             @OA\Property(property="description", type="string", example="Berbagai aksesoris laptop / notebook"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Kategori produk berhasil dibuat")
     * )
     */
    public function store(SaveProductCategoryRequest $request)
    {
        $this->authorize('create');
        $category = new ProductCategory($request->validated());
        $category->save();
        return ApiResponse::success('Kategori produk baru berhasil dibuat', $category);
    }

    /**
     * Menampilkan detail kategori produk berdasarkan ID.
     *
     * @OA\Get(
     *     path="/api/v1/product-categories/{id}",
     *     operationId="getProductCategoryById",
     *     tags={"ProductCategories"},
     *     summary="Ambil detail kategori produk berdasarkan ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari kategori produk",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data kategori produk",
     *         @OA\JsonContent(ref="#/components/schemas/ProductCategory")
     *     ),
     *     @OA\Response(response=404, description="Kategori produk tidak ditemukan"),
     *
     * )
     */
    public function show($id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->authorize('view', $category);
        return ApiResponse::success('Detail kategori produk', $category);
    }

    /**
     * Memperbarui kategori produk berdasarkan ID.
     *
     * @OA\Put(
     *     path="/api/v1/product-categories/{id}",
     *     operationId="updateProductCategory",
     *     tags={"ProductCategories"},
     *     summary="Update data kategori produk",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari kategori produk",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Aksesoris Laptop"),
     *             @OA\Property(property="description", type="string", example="Berbagai aksesoris laptop / notebook"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Kategori produk berhasil diupdate"),
     *     @OA\Response(response=404, description="Kategori produk tidak ditemukan")
     * )
     */
    public function update(SaveProductCategoryRequest $request, $id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->authorize('update', $category);
        $category->fill($request->validated());
        $category->save();
        return ApiResponse::success('Kategori produk telah diperbarui.', $category);
    }

    /**
     * Menghapus kategori produk berdasarkan ID.
     *
     * @OA\Delete(
     *     path="/api/v1/product-categories/{id}",
     *     operationId="deleteProductCategory",
     *     tags={"ProductCategories"},
     *     summary="Hapus kategori produk berdasarkan ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID dari kategori produk",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Kategori produk berhasil dihapus"),
     *     @OA\Response(response=404, description="Kategori produk tidak ditemukan")
     * )
     */
    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->authorize('delete', $category);

        $category->delete();

        return ApiResponse::success('Kategori produk telah dihapus');
    }
}
