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

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class Controller
 *
 * Base controller class that provides utility methods
 * for handling common request patterns such as sorting validation.
 */
abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Validates sort parameters in the request against allowed columns and sort directions.
     *
     * @param Request $request
     * @param array $allowedColumns List of allowed column names for sorting.
     * @return JsonResponse|null Returns error response if invalid, or null if valid.
     */
    protected function validateSort(Request $request, array $allowedColumns): ?JsonResponse
    {
        $orderBy = $request->get('order_by', 'id');
        $orderType = strtolower($request->get('order_type', 'asc'));

        if (!in_array($orderBy, $allowedColumns, true)) {
            return response()->json(['message' => 'Invalid sort column'], 400);
        }

        if (!in_array($orderType, ['asc', 'desc'], true)) {
            return response()->json(['message' => 'Invalid sort order'], 400);
        }

        return null;
    }

    /**
     * Extracts sanitized sort parameters from the request.
     *
     * @param Request $request
     * @param string $defaultColumn
     * @param string $defaultOrder
     * @return array [$orderBy, $orderType]
     */
    protected function getSortParams(Request $request, string $defaultColumn = 'id', string $defaultOrder = 'asc'): array
    {
        $orderBy = $request->get('order_by', $defaultColumn);
        $orderType = strtolower($request->get('order_type', $defaultOrder));

        return [$orderBy, $orderType];
    }
}
