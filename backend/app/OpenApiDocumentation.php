<?php

namespace App;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="My API",
 *     description="Dokumentasi REST API untuk My Project.",
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
