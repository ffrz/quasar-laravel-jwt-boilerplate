<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Hanya untuk admin atau demo akses protected route
    public function index()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }
}
