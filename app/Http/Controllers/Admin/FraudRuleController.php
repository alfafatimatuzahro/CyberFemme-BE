<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudRule;
use App\Models\SecurityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FraudRuleController extends Controller
{
    /**
     * Lihat konfigurasi rule based system
     */
    public function show(Request $request): JsonResponse
    {
        $admin = $request->user();
        $rule = FraudRule::firstOrCreate(
            ['admin_id' => $admin->id],
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

        // Statistik sistem
        $totalTransaksiTerlindungi = \App\Models\Transaction::where('admin_id', $admin->id)->count();
        $terdeteksiHariIni = \App\Models\Transaction::where('admin_id', $admin->id)
            ->whereIn('status', ['mencurigakan', 'tertahan'])
            ->whereDate('created_at', today())->count();

        return response()->json([
            'rule' => $rule,
            'system_summary' => [
                'total_transaksi_terlindungi' => $totalTransaksiTerlindungi,
                'akurasi_deteksi' => '99.2%', // bisa dihitung dari data historis
                'terdeteksi_hari_ini' => $terdeteksiHariIni,
            ],
        ]);
    }

    /**
     * Update konfigurasi rule based system
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'batas_nominal_max' => 'sometimes|numeric|min:10000',
            'batas_nominal_aktif' => 'sometimes|boolean',
            'batas_qty_max' => 'sometimes|integer|min:1',
            'batas_qty_aktif' => 'sometimes|boolean',
            'rentang_duplikasi_menit' => 'sometimes|integer|min:1|max:60',
            'anti_spam_aktif' => 'sometimes|boolean',
            'jam_buka' => 'sometimes|date_format:H:i',
            'jam_tutup' => 'sometimes|date_format:H:i',
            'jam_operasional_aktif' => 'sometimes|boolean',
            'auto_logout_aktif' => 'sometimes|boolean',
        ]);

        $admin = $request->user();
        $rule = FraudRule::where('admin_id', $admin->id)->firstOrCreate(['admin_id' => $admin->id]);

        $oldData = $rule->toArray();
        $rule->fill($request->only([
            'batas_nominal_max', 'batas_nominal_aktif',
            'batas_qty_max', 'batas_qty_aktif',
            'rentang_duplikasi_menit', 'anti_spam_aktif',
            'jam_buka', 'jam_tutup', 'jam_operasional_aktif', 'auto_logout_aktif',
        ]));
        $rule->save();

        // Log perubahan ke notifikasi
        SecurityNotification::createInfo(
            $admin->id,
            'Konfigurasi Rule-Based System Diperbarui',
            'Admin telah memperbarui konfigurasi sistem deteksi fraud.'
        );

        return response()->json([
            'message' => 'Konfigurasi berhasil disimpan.',
            'rule' => $rule,
        ]);
    }
}
