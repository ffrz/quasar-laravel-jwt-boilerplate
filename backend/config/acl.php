<?php

use App\Http\Controllers\UserController;
use App\Models\User;

/**
 * Konfigurasi Access Control List (ACL) berbasis Controller dan Action.
 *
 * Konfigurasi ini menentukan hak akses setiap role terhadap method (action) tertentu
 * dalam controller. Formatnya adalah:
 *
 * [
 *     'RoleName' => [
 *         Controller::class => [
 *             'actionName1',
 *             'actionName2',
 *             ...
 *         ],
 *         ...
 *     ],
 *     ...
 * ]
 *
 * Contoh:
 * Role `User` hanya diizinkan untuk mengakses method `index` dan `show`
 * pada `UserController`.
 */
return [
    'roles' => [
        User::Role_User => [
            UserController::class => [
                'index',
                'show'
            ]
        ],
    ]
];
