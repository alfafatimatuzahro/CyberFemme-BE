<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'profile_photo_url' => $user->profile_photo_url,
                'security_question' => $user->security_question,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'profile_photo' => 'sometimes|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
        }

        $user->fill($request->only(['username', 'email']));
        $user->save();

        return response()->json(['message' => 'Profil berhasil diperbarui.', 'user' => $user]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'kata_sandi_saat_ini' => 'required|string',
            'kata_sandi_baru' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'jawaban_keamanan' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->kata_sandi_saat_ini, $user->password)) {
            return response()->json(['message' => 'Kata sandi saat ini salah.'], 422);
        }

        if (!Hash::check($request->jawaban_keamanan, $user->security_answer)) {
            return response()->json(['message' => 'Jawaban keamanan salah.'], 422);
        }

        $user->password = Hash::make($request->kata_sandi_baru);
        $user->save();

        // Revoke semua token agar perlu login ulang
        $user->tokens()->delete();

        return response()->json(['message' => 'Kata sandi berhasil diubah. Silakan login kembali.']);
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $user = $request->user();
        $adminId = $user->getEffectiveAdminId();

        $notifications = \App\Models\SecurityNotification::where('admin_id', $adminId)
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = \App\Models\SecurityNotification::where('admin_id', $adminId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markNotificationRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $adminId = $user->getEffectiveAdminId();

        \App\Models\SecurityNotification::where('id', $id)
            ->where('admin_id', $adminId)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }
}
