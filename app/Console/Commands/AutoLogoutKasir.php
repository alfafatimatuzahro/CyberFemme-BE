<?php

namespace App\Console\Commands;

use App\Models\FraudRule;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Console\Command;

class AutoLogoutKasir extends Command
{
    protected $signature = 'cyberprotect:auto-logout';
    protected $description = 'Auto logout kasir yang sudah di luar jam operasional';

    public function handle(): void
    {
        $this->info('Cek auto-logout kasir...');

        // Ambil semua fraud rules yang aktif dengan auto_logout
        $rules = FraudRule::where('jam_operasional_aktif', true)
            ->where('auto_logout_aktif', true)
            ->get();

        foreach ($rules as $rule) {
            if (!$rule->isWithinOperationalHours()) {
                // Ambil semua kasir aktif milik admin ini
                $kasirs = User::where('admin_id', $rule->admin_id)
                    ->where('role', 'kasir')
                    ->where('is_active', true)
                    ->get();

                foreach ($kasirs as $kasir) {
                    if ($kasir->tokens()->exists()) {
                        $kasir->tokens()->delete();

                        LoginLog::where('user_id', $kasir->id)
                            ->whereNull('logout_at')
                            ->update([
                                'logout_at' => now(),
                                'keterangan' => 'Auto-logout otomatis: di luar jam operasional',
                            ]);

                        $this->line("  → Kasir {$kasir->username} di-logout otomatis.");
                    }
                }
            }
        }

        $this->info('Selesai.');
    }
}
