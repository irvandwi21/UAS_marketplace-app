<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    // Ambil data profil admin yang sedang login
    public function profile()
    {
        $admin = Auth::user();

        return response()->json([
            'name'    => $admin->name,
            'email'   => $admin->email,
            'phone'   => $admin->phone,
            'address' => $admin->address,
        ]);
    }

    // Update profil admin
    public function updateProfile(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $admin = Auth::user();

        $admin->update([
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'name'    => $admin->name,
                'email'   => $admin->email,
                'phone'   => $admin->phone,
                'address' => $admin->address,
            ]
        ]);
    }

    // Update password admin
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        $admin = Auth::user();

        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json([
                'message' => 'Password lama salah'
            ], 422);
        }

        $admin->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah'
        ]);
    }
}