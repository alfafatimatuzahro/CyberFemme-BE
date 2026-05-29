<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\SecurityNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Ringkasan keamanan dan log login
     */
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();

        // Ambil semua kasir milik admin ini
        $kasirIds = User::where('admin_id', $admin->id)->where('role', 'kasir')->pluck('id');
        // Tambahkan admin sendiri
        $userIds = $kasirIds->push($admin->id);

        $logs = LoginLog::with('user')
            ->whereIn('user_id', $userIds)
            ->latest()
            ->paginate(10);

        $totalLogin24j = LoginLog::whereIn('user_id', $userIds)
            ->where('created_at', '>=', now()->subHours(24))->count();

        $loginGagal = LoginLog::whereIn('user_id', $userIds)
            ->where('status', 'gagal')
            ->where('created_at', '>=', now()->subHours(24))->count();

        $sesiAktif = User::whereIn('id', $userIds)->withCount('tokens')->get()
            ->sum('tokens_count');

        $ancamanTerdeteksi = LoginLog::whereIn('user_id', $userIds)
            ->where('status', 'mencurigakan')
            ->whereDate('created_at', today())->count();

        return response()->json([
            'logs' => $logs,
            'summary' => [
                'total_login_24j' => $totalLogin24j,
                'login_gagal' => $loginGagal,
                'sesi_aktif' => $sesiAktif,
                'ancaman_terdeteksi' => $ancamanTerdeteksi,
            ],
        ]);
    }

    /**
     * Force logout karyawan
     */
    public function forceLogout(Request $request, User $kasir): JsonResponse
    {
        if ($kasir->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $kasir->tokens()->delete();

        // Update login log
        LoginLog::where('user_id', $kasir->id)
            ->whereNull('logout_at')
            ->update(['logout_at' => now(), 'force_logout' => true]);

        SecurityNotification::createInfo(
            $request->user()->id,
            'Force Logout Dilakukan',
            "Sesi akun {$kasir->username} telah dipaksa keluar oleh admin."
        );

        return response()->json(['message' => "Sesi {$kasir->username} berhasil dihentikan."]);
    }

    /**
     * Semua notifikasi keamanan admin
     */
    public function notifications(Request $request): JsonResponse
    {
        $admin = $request->user();

        $notifications = SecurityNotification::where('admin_id', $admin->id)
            ->latest()
            ->paginate(20);

        $unreadCount = SecurityNotification::where('admin_id', $admin->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu atau semua notifikasi sebagai dibaca
     */
    public function markRead(Request $request): JsonResponse
    {
        $admin = $request->user();

        if ($request->id) {
            SecurityNotification::where('id', $request->id)
                ->where('admin_id', $admin->id)
                ->update(['is_read' => true, 'read_at' => now()]);
        } else {
            SecurityNotification::where('admin_id', $admin->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca.']);
    }
}
