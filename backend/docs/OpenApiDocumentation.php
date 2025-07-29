<?php

namespace Docs;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Quasar + Laravel Starter Kit API",
 *     description="Dokumentasi REST API untuk proyek Anda.",
 *     @OA\Contact(
 *         email="fahmifauzirahman@gmail.com"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApiDocumentation {}
