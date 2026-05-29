<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\SecurityNotification;
use App\Models\User;
use App\Models\FraudRule;
use App\Services\GeoLocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private GeoLocationService $geoService) {}

    /**
     * Login untuk kasir (user)
     */
    public function loginKasir(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
                    ->where('role', 'kasir')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Log percobaan gagal
            if ($user) {
                LoginLog::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'lokasi' => $this->geoService->getLocation($request->ip()),
                    'user_agent' => $request->userAgent(),
                    'status' => 'gagal',
                    'keterangan' => 'Password salah',
                ]);
            }
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'Akun Anda telah dinonaktifkan. Hubungi admin.'], 403);
        }

        // Cek jam operasional
        $fraudRule = FraudRule::where('admin_id', $user->admin_id)->first();
        if ($fraudRule && $fraudRule->jam_operasional_aktif) {
            if (!$fraudRule->isWithinOperationalHours()) {
                return response()->json([
                    'message' => 'Login tidak diizinkan di luar jam operasional (' . $fraudRule->jam_buka . ' - ' . $fraudRule->jam_tutup . ').',
                ], 403);
            }
        }

        $ip = $request->ip();
        $lokasi = $this->geoService->getLocation($ip);
        $isSuspicious = $this->geoService->isSuspiciousLocation($ip);

        $logStatus = $isSuspicious ? 'mencurigakan' : 'sukses';

        $log = LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'lokasi' => $lokasi,
            'user_agent' => $request->userAgent(),
            'status' => $logStatus,
            'keterangan' => $isSuspicious ? 'Login dari lokasi tidak biasa' : null,
        ]);

        // Notifikasi admin jika mencurigakan
        if ($isSuspicious && $user->admin_id) {
            $notif = SecurityNotification::createLoginAlert($user->admin_id, $log);
            event(new \App\Events\LoginSuspicious($log, $user, $user->admin_id));
        }

        // Revoke semua token lama, buat yang baru
        $user->tokens()->delete();
        $token = $user->createToken('kasir-token', ['role:kasir'])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'profile_photo_url' => $user->profile_photo_url,
                'admin_id' => $user->admin_id,
            ],
            'fraud_rule' => $fraudRule ? [
                'jam_buka' => $fraudRule->jam_buka,
                'jam_tutup' => $fraudRule->jam_tutup,
                'auto_logout_aktif' => $fraudRule->auto_logout_aktif,
            ] : null,
        ]);
    }

    /**
     * Login untuk admin (pemilik UMKM)
     */
    public function loginAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'nama_umkm' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nama_umkm', $request->nama_umkm)
                    ->where('role', 'admin')
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nama_umkm' => ['Nama UMKM atau password salah.'],
            ]);
        }

        $ip = $request->ip();
        $lokasi = $this->geoService->getLocation($ip);
        $isSuspicious = $this->geoService->isSuspiciousLocation($ip);

        LoginLog::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'lokasi' => $lokasi,
            'user_agent' => $request->userAgent(),
            'status' => $isSuspicious ? 'mencurigakan' : 'sukses',
        ]);

        $user->tokens()->delete();
        $token = $user->createToken('admin-token', ['role:admin'])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'nama_umkm' => $user->nama_umkm,
                'profile_photo_url' => $user->profile_photo_url,
            ],
        ]);
    }

    /**
     * Register admin baru
     */
    public function registerAdmin(Request $request): JsonResponse
    {
        $request->validate([
            'nama_umkm' => 'required|string|unique:users,nama_umkm',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
        ]);

        $user = User::create([
            'username' => \Illuminate\Support\Str::slug($request->nama_umkm),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'nama_umkm' => $request->nama_umkm,
            'security_question' => $request->security_question,
            'security_answer' => Hash::make($request->security_answer),
        ]);

        // Buat fraud rules default
        FraudRule::create(['admin_id' => $user->id]);

        return response()->json([
            'message' => 'Akun berhasil dibuat. Silakan login.',
            'user_id' => $user->id,
        ], 201);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        // Update login log
        LoginLog::where('user_id', $request->user()->id)
            ->whereNull('logout_at')
            ->latest()
            ->first()
            ?->update(['logout_at' => now()]);

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout.']);
    }

    /**
     * Cek status jam operasional (untuk auto-logout frontend)
     */
    public function checkOperationalHours(Request $request): JsonResponse
    {
        $user = $request->user();
        $adminId = $user->getEffectiveAdminId();
        $fraudRule = FraudRule::where('admin_id', $adminId)->first();

        if (!$fraudRule) {
            return response()->json(['within_hours' => true]);
        }

        return response()->json([
            'within_hours' => $fraudRule->isWithinOperationalHours(),
            'auto_logout_aktif' => $fraudRule->auto_logout_aktif,
            'jam_buka' => $fraudRule->jam_buka,
            'jam_tutup' => $fraudRule->jam_tutup,
        ]);
    }
}
