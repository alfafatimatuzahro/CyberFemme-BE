<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\FraudDetectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Buat transaksi baru dari kasir
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_pelanggan' => 'nullable|string|max:255',
            'loyalty_id' => 'nullable|string',
            'metode_bayar' => 'required|in:cash,qris,debit,kredit',
            'nominal_bayar' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.diskon_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        $kasir = $request->user();
        $adminId = $kasir->admin_id;

        DB::beginTransaction();
        try {
            // Hitung subtotal dari setiap item
            $itemsData = [];
            $subtotal = 0;

            foreach ($request->items as $itemReq) {
                $product = Product::where('id', $itemReq['product_id'])
                    ->where('admin_id', $adminId)
                    ->where('is_active', true)
                    ->firstOrFail();

                $diskon = $itemReq['diskon_persen'] ?? 0;
                $hargaSetelahDiskon = $product->harga * (1 - $diskon / 100);
                $itemSubtotal = $hargaSetelahDiskon * $itemReq['qty'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'nama_produk' => $product->nama,
                    'sku' => $product->sku,
                    'harga_satuan' => $product->harga,
                    'qty' => $itemReq['qty'],
                    'diskon_persen' => $diskon,
                    'subtotal' => $itemSubtotal,
                ];
            }

            $ppn = $subtotal * 0.10;
            $total = $subtotal + $ppn;
            $kembalian = max(0, $request->nominal_bayar - $total);

            $transactionData = [
                'kasir_id' => $kasir->id,
                'admin_id' => $adminId,
                'metode_bayar' => $request->metode_bayar,
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'total' => $total,
                'nominal_bayar' => $request->nominal_bayar,
                'kembalian' => $kembalian,
            ];

            // Analisis fraud
            $fraudService = new FraudDetectionService($adminId);
            $fraudResult = $fraudService->analyze($transactionData, $itemsData);

            // Buat transaksi
            $transaction = Transaction::create(array_merge($transactionData, [
                'invoice_id' => Transaction::generateInvoiceId(),
                'nama_pelanggan' => $request->nama_pelanggan,
                'loyalty_id' => $request->loyalty_id,
                'status' => $fraudResult['status'],
                'fraud_reason' => $fraudResult['reason'],
                'fraud_id' => $fraudResult['fraud_id'],
                'terminal_id' => 'T-' . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT),
            ]));

            // Simpan items
            foreach ($itemsData as $item) {
                $transaction->items()->create($item);
            }

            // Kirim notifikasi jika fraud / tertahan
            $fraudService->notifyIfFraud($transaction);

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil diproses.',
                'transaction' => $transaction->load('items.product', 'kasir'),
                'is_fraud' => $fraudResult['is_fraud'],
                'status' => $fraudResult['status'],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Riwayat transaksi kasir yang login
     */
    public function index(Request $request): JsonResponse
    {
        $kasir = $request->user();

        $query = Transaction::with('items')
            ->where('kasir_id', $kasir->id)
            ->latest();

        if ($request->search) {
            $query->where('invoice_id', 'like', "%{$request->search}%");
        }

        $transactions = $query->paginate(10);

        $todaySales = Transaction::where('kasir_id', $kasir->id)
            ->where('status', 'sukses')
            ->whereDate('created_at', today())
            ->sum('total');

        $successCount = Transaction::where('kasir_id', $kasir->id)
            ->whereDate('created_at', today())
            ->where('status', 'sukses')
            ->count();

        $anomalyCount = Transaction::where('kasir_id', $kasir->id)
            ->whereDate('created_at', today())
            ->whereIn('status', ['mencurigakan', 'tertahan'])
            ->count();

        return response()->json([
            'transactions' => $transactions,
            'summary' => [
                'total_penjualan_hari_ini' => $todaySales,
                'transaksi_sukses' => $successCount,
                'anomali_terdeteksi' => $anomalyCount,
            ],
        ]);
    }

    /**
     * Detail transaksi
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        // Kasir hanya bisa lihat transaksinya sendiri
        if ($transaction->kasir_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        return response()->json([
            'transaction' => $transaction->load('items.product', 'kasir', 'admin'),
        ]);
    }

    /**
     * Dashboard kasir - ringkasan hari ini
     */
    public function dashboard(Request $request): JsonResponse
    {
        $kasir = $request->user();
        $adminId = $kasir->admin_id;

        $today = today();

        $totalTransaksi = Transaction::where('kasir_id', $kasir->id)
            ->whereDate('created_at', $today)->count();

        $totalOmzet = Transaction::where('kasir_id', $kasir->id)
            ->where('status', 'sukses')
            ->whereDate('created_at', $today)->sum('total');

        // Data chart per jam (8 jam terakhir)
        $chartData = [];
        for ($h = 8; $h <= 20; $h += 3) {
            $count = Transaction::where('kasir_id', $kasir->id)
                ->whereDate('created_at', $today)
                ->whereTime('created_at', '>=', sprintf('%02d:00:00', $h))
                ->whereTime('created_at', '<', sprintf('%02d:00:00', $h + 3))
                ->count();
            $chartData[] = ['jam' => sprintf('%02d:00', $h), 'jumlah' => $count];
        }

        return response()->json([
            'kasir' => [
                'username' => $kasir->username,
                'profile_photo_url' => $kasir->profile_photo_url,
            ],
            'total_transaksi' => $totalTransaksi,
            'total_omzet' => $totalOmzet,
            'chart_data' => $chartData,
        ]);
    }
}
