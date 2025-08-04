<?php

namespace Docs\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="ProductCategory",
 *     required={"id", "name"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Aksesoris laptop"),
 *     @OA\Property(property="description", type="string", example="Deskripsi kategori aksesoris laptop"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T15:30:00Z"),
 * )
 */
class ProductCategorySchema {}
