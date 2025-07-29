<?php

namespace Docs\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     required={"id", "name", "email"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="Fahmi Fauzi"),
 *     @OA\Property(property="role", type="string", enum={"admin", "user"}, example="admin"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="email", type="string", format="email", example="fahmi@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T15:30:00Z"),
 * )
 */
class UserSchema {}
