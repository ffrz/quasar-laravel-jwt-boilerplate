<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class Registrar
{
    public function handle(Exceptions $exceptions): void
    {
        $exceptions->render(function (ValidationException $e, Request $request) {
            return ApiResponse::error('Validation error', 422, $e->validator->errors());
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            return ApiResponse::error('Data tidak ditemukan', 404, $e->getMessage());
        });

        // $exceptions->render(function (AuthenticationException $e, Request $request) {
        //     return ApiResponse::error('Unauthenticated', 401);
        // });

        // $exceptions->render(function (NotFoundHttpException $e, Request $request) {
        //     return ApiResponse::error('Resource not found', 404);
        // });
    }
}
