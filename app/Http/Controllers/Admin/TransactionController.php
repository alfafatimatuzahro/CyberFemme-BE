<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\SecurityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Daftar semua transaksi (dengan filter)
     */
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();

        $query = Transaction::with(['kasir', 'items'])
            ->where('admin_id', $admin->id)
            ->latest();

        // Filter kasir
        if ($request->kasir_id) {
            $query->where('kasir_id', $request->kasir_id);
        }

        // Filter tanggal
        if ($request->tanggal_mulai) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->tanggal_selesai) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Filter status
        if ($request->status && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Search invoice
        if ($request->search) {
            $query->where('invoice_id', 'like', "%{$request->search}%");
        }

        $transactions = $query->paginate(10);

        // Summary
        $today = today();
        $totalSukses = Transaction::where('admin_id', $admin->id)
            ->whereDate('created_at', $today)->where('status', 'sukses')->count();
        $totalMencurigakan = Transaction::where('admin_id', $admin->id)
            ->whereDate('created_at', $today)->where('status', 'mencurigakan')->count();
        $totalOmzet = Transaction::where('admin_id', $admin->id)
            ->whereDate('created_at', $today)->where('status', 'sukses')->sum('total');

        return response()->json([
            'transactions' => $transactions,
            'summary' => [
                'transaksi_sukses' => $totalSukses,
                'transaksi_mencurigakan' => $totalMencurigakan,
                'total_omzet_hari_ini' => $totalOmzet,
                'status_keamanan' => $totalMencurigakan > 0 ? 'Butuh Perhatian' : 'Sistem Aman / Normal',
            ],
        ]);
    }

    /**
     * Detail transaksi
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        return response()->json([
            'transaction' => $transaction->load('items.product', 'kasir', 'reviewer'),
        ]);
    }

    /**
     * Admin setujui atau tolak transaksi mencurigakan
     */
    public function review(Request $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $request->validate([
            'action' => 'required|in:setujui,tolak',
            'catatan' => 'nullable|string',
            'metode_bayar' => 'required|in:cash,qris',
        ]);

        $newStatus = $request->action === 'setujui' ? 'sukses' : 'ditolak';

        $transaction->update([
            'status' => $newStatus,
            'admin_reviewed' => true,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        // Tandai notifikasi terkait sebagai dibaca
        SecurityNotification::where('ref_type', 'transaction')
            ->where('ref_id', $transaction->id)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Notifikasi ke kasir (via database)
        $kasirAdminId = $transaction->kasir->admin_id;
        SecurityNotification::create([
            'admin_id' => $kasirAdminId,
            'type' => 'info',
            'judul' => 'Transaksi ' . ucfirst($newStatus),
            'pesan' => "Transaksi {$transaction->invoice_id} telah di-review oleh admin dan statusnya diubah menjadi {$newStatus}.",
            'ref_type' => 'transaction',
            'ref_id' => $transaction->id,
        ]);

        return response()->json([
            'message' => "Transaksi berhasil di-{$request->action}.",
            'transaction' => $transaction->fresh(),
        ]);
    }

    public function dashboard(Request $request)
{
    $kasir = $request->user();

    $today = today();

    $totalTransaksi = Transaction::where(
        'kasir_id',
        $kasir->id
    )
    ->whereDate('created_at', $today)
    ->count();

    $totalOmzet = Transaction::where(
        'kasir_id',
        $kasir->id
    )
    ->where('status', 'sukses')
    ->whereDate('created_at', $today)
    ->sum('total');

    return response()->json([
        'total_transaksi' => $totalTransaksi,
        'total_omzet' => $totalOmzet
    ]);
}
}
