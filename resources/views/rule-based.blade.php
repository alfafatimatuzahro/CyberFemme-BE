<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rule Based System - CyberProtect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-stretch h-screen text-sm overflow-hidden">

    <aside class="w-64 bg-[#0B2046] text-white flex flex-col justify-between shrink-0 h-full">
        <div>
            <div class="p-6 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-2xl"></i>
                <h1 class="font-bold leading-tight">CyberProtect<br>UMKM</h1>
            </div>
            <nav class="flex flex-col gap-2 px-4 mt-4">
                <a href="{{ route('dashboard') }}" class="py-2 hover:text-gray-300 {{ Route::is('dashboard') ? 'font-bold underline' : '' }}">
                    Transaksi & Fraud
                </a>

                <a href="{{ route('ringkasan-keamanan') }}" class="py-2 hover:text-gray-300 {{ Route::is('ringkasan-keamanan') ? 'font-bold underline' : '' }}">
                    Ringkasan Keamanan & Log Aktivitas Login
                </a>

                <a href="{{ route('manajemen-karyawan') }}" class="py-2 hover:text-gray-300 {{ Route::is('manajemen-karyawan') ? 'font-bold underline' : '' }}">
                    Manajemen Karyawan
                </a>

                <a href="{{ route('rule-based') }}" class="py-2 hover:text-gray-300 {{ Route::is('rule-based') ? 'font-bold underline' : '' }}">
                    Rule Based System
                </a>
            </nav>
        </div>
        <div class="p-6">
            <a href="{{ route('profil') }}" class="font-bold"><i class="fa-solid fa-user mr-2"></i> Profil</a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative h-full overflow-y-auto">
        <header class="bg-white border-b border-gray-200 p-4 flex justify-between items-center z-10">
            <h2 class="text-2xl font-bold text-[#0B2046]">Rule Based System</h2>

            <div class="flex items-center gap-4 relative">
                <button onclick="toggleElement('profilePopup')" class="flex items-center gap-2 font-semibold">
                    @if(auth()->user()->profile_photo)

                    <img
                        src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                        class="w-8 h-8 rounded-full object-cover">

                    @else

                    <div class="w-8 h-8 rounded-full bg-gray-300 overflow-hidden"></div>

                    @endif
                    {{ auth()->user()->nama_umkm ?? 'Username' }}
                </button>

                <button onclick="toggleElement('notifPopup')"><i class="fa-regular fa-bell text-lg"></i></button>

                <div id="profilePopup" class="hidden absolute top-12 right-10 w-48 bg-white border border-gray-200 shadow-lg rounded-xl p-4 text-center z-50">
                    <div class="flex items-center gap-2 font-bold mb-3 border-b pb-2">
                        <i class="fa-solid fa-user-circle text-gray-400 text-xl"></i>
                        {{ auth()->user()->nama_umkm ?? 'USERNAME' }}
                    </div>
                    <a href="/login" class="block w-full bg-[#0B2046] text-white rounded py-2 text-center text-xs font-bold hover:bg-slate-800">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> LOG OUT
                    </a>
                </div>

                <div id="notifPopup" class="hidden absolute top-12 right-0 w-80 bg-white border border-gray-200 shadow-lg rounded-xl p-4 text-sm z-50">
                    <div class="flex gap-4 mb-4">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                        <div>
                            <p class="font-bold text-xs"><span class="bg-red-200 text-red-800 px-1 rounded text-[10px]">PERINGATAN</span> Ancaman Keamanan Terdeteksi</p>
                            <p class="text-xs text-gray-600">Beberapa aktivitas login mencurigakan telah terdeteksi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <form action="{{ route('rule-based.update') }}" method="POST">
        @csrf
        @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
        @endif
        <div class="p-8 flex gap-6">
            <div class="flex-1 flex flex-col gap-6">
                <div class="bg-white p-6 border rounded-2xl shadow-sm">
                    <div class="flex justify-between items-center mb-4">

                        <h3 class="font-bold flex items-center gap-2">
                            <i class="fa-solid fa-wallet"></i>
                            Batas Maksimal Nominal
                        </h3>

                        <div class="flex items-center gap-2">

                            @if($rule->batas_nominal_aktif)

                                <span class="text-green-600 bg-green-50 px-2 py-1 rounded text-[10px] font-bold">
                                    AKTIF
                                </span>

                            @else

                                <span class="text-red-600 bg-red-50 px-2 py-1 rounded text-[10px] font-bold">
                                    NONAKTIF
                                </span>

                            @endif

                        </div>

                    </div>
                    <p class="text-xs text-gray-400 mb-2">Transaksi di atas angka ini akan otomatis ditahan.</p>
                    <input type="number"name="batas_nominal_max"value="{{ $rule->batas_nominal_max }}"class="w-full border p-3 rounded-xl font-bold text-lg">
                    <p class="text-xs text-gray-400 mt-2 italic">Saran: Untuk UMKM retail, batas aman biasanya Rp 500.000 per transaksi.</p>
                </div>

                <div class="bg-white p-6 border rounded-2xl shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold flex items-center gap-2"><i class="fa-solid fa-rotate-left"></i> Batasan Pembelian Per Item</h3>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">Mencegah pembelian jumlah item yang berlebihan dalam satu transaksi.</p>
                    <div class="flex items-center gap-2">
                        <input type="number"name="batas_qty_max"value="{{ $rule->batas_qty_max }}"class="w-20 border p-2 rounded-xl text-center">
                        <span>Pcs</span>
                    </div>
                </div>

                <div class="bg-white p-6 border rounded-2xl shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold flex items-center gap-2"><i class="fa-solid fa-clock"></i> Jam Operasional Ketat</h3>
                    </div>
                    <p class="text-xs text-gray-400 mb-4">Membatasi akses sistem di luar jam kerja yang ditentukan.</p>
                    <div class="flex gap-4">
                        <div><label class="block text-[10px] font-bold">BUKA</label><input type="time"name="jam_buka"value="{{ $rule->jam_buka }}"class="border p-2 rounded-lg">
                        <div><label class="block text-[10px] font-bold">TUTUP</label><input type="time"name="jam_tutup"value="{{ $rule->jam_tutup }}"class="border p-2 rounded-lg">
                        <div class="flex items-end"><button class="bg-blue-50 text-blue-700 px-4 py-2 rounded-xl font-bold"><i class="fa-solid fa-lock mr-2"></i> Aktifkan Kunci Otomatis</button></div>
                    </div>
                </div>
            </div>

            <div class="w-80 flex flex-col gap-6">
                <div class="bg-[#0B2046] text-white p-6 rounded-2xl">
                    <h3 class="font-bold mb-4">Ringkasan Sistem</h3>
                    <p class="text-[10px] text-gray-300">Logika penyaringan Anda saat ini melindungi {{ $jumlahTransaksi }} transaksi.</p>
                    <div class="my-6">
                        <p class="text-xs text-gray-400">Akurasi Deteksi</p>
                        <p class="text-2xl font-bold text-green-400">{{ $akurasi }}%</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Terdeteksi Hari Ini</p>
                        <p class="text-2xl font-bold">{{ $jumlahKasus }} Kasus</p>
                    </div>
                </div>

                <div class="bg-white p-6 border rounded-2xl">
                    <h4 class="text-[10px] font-bold text-gray-400 uppercase mb-4">Log Perubahan Terakhir</h4>
                    <div class="border-l-2 border-green-500 pl-3 mb-4">
                        <p class="font-bold text-xs">Jam Operasional Diperbarui</p>
                        <p class="text-[10px] text-gray-500">Oleh: Admin Utama • 2 jam yang lalu</p>
                    </div>
                    <div class="border-l-2 border-gray-300 pl-3">
                        <p class="font-bold text-xs">Batas Nominal Diturunkan</p>
                        <p class="text-[10px] text-gray-500">Oleh: Admin Utama • Kemarin</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-4 border-t pt-6">
            <button class="px-6 py-2 rounded-xl border font-bold hover:bg-gray-100">Batalkan Perubahan</button>
            <button type="submit"class="px-6 py-2 rounded-xl bg-black text-white font-bold hover:bg-gray-800"><i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Konfigurasi</button>
        </div>
        </form>
    </main>
        <script>
    function toggleElement(id) {
        // Ambil elemen yang ingin dibuka
        const element = document.getElementById(id);
        
        // Daftar semua ID popup yang ada
        const popups = ['profilePopup', 'notifPopup'];
        
        // Tutup popup lain jika ada yang terbuka
        popups.forEach(popupId => {
            if (popupId !== id) {
                document.getElementById(popupId).classList.add('hidden');
            }
        });

        // Toggle (buka/tutup) elemen yang diklik
        element.classList.toggle('hidden');
    }

    // Opsional: Tutup popup jika mengklik di luar area popup
    window.onclick = function(event) {
        if (!event.target.matches('button') && !event.target.closest('.relative')) {
            document.getElementById('profilePopup').classList.add('hidden');
            document.getElementById('notifPopup').classList.add('hidden');
        }
    }
</script>
</body>
</html>