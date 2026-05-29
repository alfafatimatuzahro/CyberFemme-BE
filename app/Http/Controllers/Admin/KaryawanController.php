<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    /**
     * Daftar karyawan kasir milik admin
     */
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();

        $query = User::where('admin_id', $admin->id)
            ->where('role', 'kasir')
            ->latest();

        $kasirs = $query->paginate(10);

        return response()->json([
            'kasirs' => $kasirs,
            'summary' => [
                'total_akun_staf' => User::where('admin_id', $admin->id)->where('role', 'kasir')->count(),
                'akun_aktif' => User::where('admin_id', $admin->id)->where('role', 'kasir')->where('is_active', true)->count(),
                'upaya_login_24j' => LoginLog::whereHas('user', fn($q) => $q->where('admin_id', $admin->id))
                    ->where('created_at', '>=', now()->subHours(24))->count(),
            ],
        ]);
    }

    /**
     * Tambah kasir baru
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
        ]);

        $admin = $request->user();

        // Generate password sementara
        $tempPassword = 'CP_' . strtoupper(Str::random(6));

        $kasir = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($tempPassword),
            'role' => 'kasir',
            'admin_id' => $admin->id,
            'temp_password' => $tempPassword,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Akun kasir berhasil dibuat.',
            'kasir' => [
                'id' => $kasir->id,
                'username' => $kasir->username,
                'email' => $kasir->email,
                'temp_password' => $tempPassword,
                'created_at' => $kasir->created_at,
            ],
        ], 201);
    }

    /**
     * Update data kasir
     */
    public function update(Request $request, User $kasir): JsonResponse
    {
        if ($kasir->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $request->validate([
            'username' => 'sometimes|string|unique:users,username,' . $kasir->id,
            'email' => 'sometimes|email|unique:users,email,' . $kasir->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $kasir->fill($request->only(['username', 'email', 'is_active']));
        $kasir->save();

        // Jika dinonaktifkan, revoke semua token
        if ($request->has('is_active') && !$request->is_active) {
            $kasir->tokens()->delete();
        }

        return response()->json(['message' => 'Data kasir berhasil diperbarui.', 'kasir' => $kasir]);
    }

    /**
     * Hapus kasir
     */
    public function destroy(Request $request, User $kasir): JsonResponse
    {
        if ($kasir->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $kasir->tokens()->delete();
        $kasir->delete();

        return response()->json(['message' => 'Akun kasir berhasil dihapus.']);
    }

    /**
     * Reset password kasir (generate ulang password sementara)
     */
    public function resetPassword(Request $request, User $kasir): JsonResponse
    {
        if ($kasir->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $tempPassword = 'CP_' . strtoupper(Str::random(6));
        $kasir->update([
            'password' => Hash::make($tempPassword),
            'temp_password' => $tempPassword,
        ]);
        $kasir->tokens()->delete();

        return response()->json([
            'message' => 'Password kasir berhasil direset.',
            'temp_password' => $tempPassword,
        ]);
    }
}
