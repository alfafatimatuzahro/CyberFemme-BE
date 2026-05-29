<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $admin = $request->user();
        return response()->json([
            'user' => [
                'id' => $admin->id,
                'username' => $admin->username,
                'email' => $admin->email,
                'nama_umkm' => $admin->nama_umkm,
                'alamat_umkm' => $admin->alamat_umkm,
                'role' => $admin->role,
                'profile_photo_url' => $admin->profile_photo_url,
                'security_question' => $admin->security_question,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $admin = $request->user();

        $request->validate([
            'nama_umkm' => 'sometimes|string|unique:users,nama_umkm,' . $admin->id,
            'email' => 'sometimes|email|unique:users,email,' . $admin->id,
            'alamat_umkm' => 'sometimes|string',
            'profile_photo' => 'sometimes|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $admin->profile_photo = $path;
        }

        $admin->fill($request->only(['nama_umkm', 'email', 'alamat_umkm']));
        $admin->save();

        return response()->json(['message' => 'Profil berhasil diperbarui.', 'user' => $admin]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'kata_sandi_saat_ini' => 'required|string',
            'kata_sandi_baru' => ['required', 'confirmed', Password::min(8)],
            'jawaban_keamanan' => 'required|string',
        ]);

        $admin = $request->user();

        if (!Hash::check($request->kata_sandi_saat_ini, $admin->password)) {
            return response()->json(['message' => 'Kata sandi saat ini salah.'], 422);
        }

        if (!Hash::check($request->jawaban_keamanan, $admin->security_answer)) {
            return response()->json(['message' => 'Jawaban keamanan salah.'], 422);
        }

        $admin->password = Hash::make($request->kata_sandi_baru);
        $admin->save();
        $admin->tokens()->delete();

        return response()->json(['message' => 'Kata sandi berhasil diubah. Silakan login kembali.']);
    }
}
