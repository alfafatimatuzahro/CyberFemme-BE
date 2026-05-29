<?php

namespace App\Services;

use App\Models\FraudRule;
use App\Models\Transaction;
use App\Models\SecurityNotification;
use Carbon\Carbon;

class FraudDetectionService
{
    protected FraudRule $rule;
    protected int $adminId;

    public function __construct(int $adminId)
    {
        $this->adminId = $adminId;
        $this->rule = FraudRule::firstOrCreate(
            ['admin_id' => $adminId],
            [
                'batas_nominal_max' => 5000000,
                'batas_nominal_aktif' => true,
                'batas_qty_max' => 20,
                'batas_qty_aktif' => true,
                'rentang_duplikasi_menit' => 5,
                'anti_spam_aktif' => true,
                'jam_buka' => '08:00:00',
                'jam_tutup' => '22:00:00',
                'jam_operasional_aktif' => true,
                'auto_logout_aktif' => true,
            ]
        );
    }

    /**
     * Analisis transaksi dan tentukan apakah mencurigakan.
     * Return array ['is_fraud' => bool, 'reason' => string|null, 'status' => string]
     */
    public function analyze(array $transactionData, array $items): array
    {
        $fraudReasons = [];

        // Rule 1: Cek batas nominal
        if ($this->rule->batas_nominal_aktif) {
            if ($transactionData['total'] > $this->rule->batas_nominal_max) {
                $fraudReasons[] = "Nominal transaksi melebihi batas maksimal (Rp " . number_format($this->rule->batas_nominal_max, 0, ',', '.') . ")";
            }
        }

        // Rule 2: Cek qty per item
        if ($this->rule->batas_qty_aktif) {
            foreach ($items as $item) {
                if ($item['qty'] > $this->rule->batas_qty_max) {
                    $fraudReasons[] = "Jumlah item \"{$item['nama_produk']}\" melebihi batas maksimal ({$this->rule->batas_qty_max} pcs)";
                    break;
                }
            }
        }

        // Rule 3: Anti-spam / duplikasi transaksi
        if ($this->rule->anti_spam_aktif) {
            $recentTransaction = Transaction::where('kasir_id', $transactionData['kasir_id'])
                ->where('total', $transactionData['total'])
                ->where('metode_bayar', $transactionData['metode_bayar'])
                ->where('status', 'sukses')
                ->where('created_at', '>=', now()->subMinutes($this->rule->rentang_duplikasi_menit))
                ->first();

            if ($recentTransaction) {
                $fraudReasons[] = "Duplikasi transaksi terdeteksi dalam {$this->rule->rentang_duplikasi_menit} menit terakhir";
            }
        }

        if (!empty($fraudReasons)) {
            return [
                'is_fraud' => true,
                'reason' => implode('; ', $fraudReasons),
                'status' => 'mencurigakan',
                'fraud_id' => 'F' . time(),
            ];
        }

        // Cek apakah perlu ditahan (nominal besar tapi tidak melebihi batas)
        $holdThreshold = $this->rule->batas_nominal_max * 0.8; // 80% dari batas
        if ($this->rule->batas_nominal_aktif && $transactionData['total'] >= $holdThreshold) {
            return [
                'is_fraud' => false,
                'reason' => 'Nominal mendekati batas maksimal, perlu verifikasi',
                'status' => 'tertahan',
                'fraud_id' => null,
            ];
        }

        return [
            'is_fraud' => false,
            'reason' => null,
            'status' => 'sukses',
            'fraud_id' => null,
        ];
    }

    /**
     * Cek apakah saat ini dalam jam operasional
     */
    public function isWithinOperationalHours(): bool
    {
        return $this->rule->isWithinOperationalHours();
    }

    /**
     * Cek apakah auto-logout aktif
     */
    public function isAutoLogoutActive(): bool
    {
        return $this->rule->auto_logout_aktif;
    }

    /**
     * Kirim notifikasi ke admin jika transaksi mencurigakan
     */
    public function notifyIfFraud(Transaction $transaction): void
    {
        if ($transaction->status === 'mencurigakan') {
            $notif = SecurityNotification::createFraudAlert($this->adminId, $transaction);

            // Broadcast real-time event
            event(new \App\Events\FraudDetected($transaction, $notif));
        } elseif ($transaction->status === 'tertahan') {
            SecurityNotification::create([
                'admin_id' => $this->adminId,
                'type' => 'warning',
                'judul' => 'Transaksi Ditahan - Perlu Verifikasi',
                'pesan' => "Transaksi {$transaction->invoice_id} ditahan karena: {$transaction->fraud_reason}",
                'ref_type' => 'transaction',
                'ref_id' => $transaction->id,
            ]);

            event(new \App\Events\TransactionHeld($transaction));
        }
    }

    public function getRule(): FraudRule
    {
        return $this->rule;
    }
}
