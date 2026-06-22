<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{
    // REGISTER ADMIN
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6'
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Register admin berhasil',
            'admin' => $admin
        ]);
    }

    // LOGIN ADMIN
      public function login(Request $request)
{
    $admin = Admin::where('email', $request->email)->first();

    if (!$admin || !Hash::check($request->password, $admin->password)) {
        return response()->json([
            'message' => 'Login admin gagal'
        ], 401);
    }

    $token = $admin->createToken('admin_token')->plainTextToken;

    return response()->json([
        'message' => 'Login admin berhasil',
        'token' => $token,
        'admin' => $admin
    ]);
}

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout admin berhasil'
        ]);
    }
}