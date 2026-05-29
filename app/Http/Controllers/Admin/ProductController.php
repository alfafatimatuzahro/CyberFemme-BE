<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user();

        $query = Product::where('admin_id', $admin->id);

        if ($request->kategori && $request->kategori !== 'semua') {
            $query->where('kategori', $request->kategori);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        return response()->json(['products' => $query->paginate(20)]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|max:2048',
        ]);

        $admin = $request->user();

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('products', 'public');
        }

        $product = Product::create([
            'admin_id' => $admin->id,
            'nama' => $request->nama,
            'sku' => Product::generateSku($request->kategori),
            'kategori' => $request->kategori,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'foto' => $fotoPath,
        ]);

        return response()->json(['message' => 'Produk berhasil ditambahkan.', 'product' => $product], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        if ($product->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'kategori' => 'sometimes|string',
            'harga' => 'sometimes|numeric|min:0',
            'stok' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $product->fill($request->only(['nama', 'kategori', 'harga', 'stok', 'is_active']));
        $product->save();

        return response()->json(['message' => 'Produk berhasil diperbarui.', 'product' => $product]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        if ($product->admin_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan.'], 403);
        }

        $product->delete();
        return response()->json(['message' => 'Produk berhasil dihapus.']);
    }

    // Untuk kasir: cari produk saat POS
    public function search(Request $request): JsonResponse
    {
        $kasir = $request->user();
        $adminId = $kasir->isAdmin() ? $kasir->id : $kasir->admin_id;

        $query = Product::where('admin_id', $adminId)->where('is_active', true);

        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('sku', 'like', "%{$request->q}%");
            });
        }
        if ($request->kategori && $request->kategori !== 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        return response()->json(['products' => $query->get()]);
    }

    // Ambil kategori unik
    public function categories(Request $request): JsonResponse
    {
        $adminId = $request->user()->getEffectiveAdminId();
        $categories = Product::where('admin_id', $adminId)
            ->distinct()->pluck('kategori');

        return response()->json(['categories' => $categories]);
    }
}
