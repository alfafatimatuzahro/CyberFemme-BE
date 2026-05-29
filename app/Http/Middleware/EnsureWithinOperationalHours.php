<?php

namespace App\Http\Middleware;

use App\Models\FraudRule;
use App\Models\LoginLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWithinOperationalHours
{
    /**
     * Middleware untuk auto-logout kasir di luar jam operasional.
     * Hanya berlaku untuk kasir (bukan admin).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya berlaku untuk kasir
        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        $fraudRule = FraudRule::where('admin_id', $user->admin_id)->first();

        if (!$fraudRule || !$fraudRule->jam_operasional_aktif || !$fraudRule->auto_logout_aktif) {
            return $next($request);
        }

        if (!$fraudRule->isWithinOperationalHours()) {
            // Revoke token
            $user->currentAccessToken()->delete();

            // Update login log
            LoginLog::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->update([
                    'logout_at' => now(),
                    'keterangan' => 'Auto-logout: di luar jam operasional',
                ]);

            return response()->json([
                'message' => 'Sesi Anda telah berakhir karena di luar jam operasional.',
                'auto_logout' => true,
                'jam_buka' => $fraudRule->jam_buka,
                'jam_tutup' => $fraudRule->jam_tutup,
            ], 401);
        }

        return $next($request);
    }
}
