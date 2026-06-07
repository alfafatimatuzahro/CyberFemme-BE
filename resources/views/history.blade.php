<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History - CyberProtect UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EBEBEB] flex min-h-screen font-sans antialiased">

    <aside class="w-64 bg-[#010915] text-white flex flex-col justify-between p-6 flex-shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-12">
                <i class="fa-solid fa-shield-halved text-blue-500 text-2xl"></i>
                <h1 class="text-lg font-bold leading-none uppercase tracking-wider">
                    CyberProtect<br><span class="text-[10px] font-black opacity-80">UMKM</span>
                </h1>
            </div>

            <nav class="space-y-3">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-4 text-gray-400 hover:text-white p-3 text-sm font-medium transition-all">
                    <i class="fa-solid fa-house w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('pos') }}" class="flex items-center gap-4 text-gray-400 hover:text-white p-3 text-sm font-medium transition-all">
                    <i class="fa-solid fa-desktop w-5 text-center"></i> POS
                </a>
                <a href="{{ route('history') }}" class="flex items-center gap-4 text-blue-400 bg-blue-50/10 p-3 text-sm font-bold rounded-xl border-l-4 border-blue-500">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> History
                </a>
            </nav>
        </div>

        <div class="border-t border-gray-800 pt-6">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 bg-white/5 p-3 rounded-xl hover:bg-white/10 transition-all border border-gray-800/50 group">
                <div class="w-9 h-9 bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-gray-600 transition-colors flex-shrink-0">
                    <i class="fa-solid fa-user text-white text-xs"></i>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-bold truncate text-white group-hover:text-blue-400 transition-colors">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                </div>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        
        <header class="flex justify-between items-center mb-6 relative">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">History</h2>
            
            <div class="flex items-center gap-3 relative">
                <button id="profileDropdownBtn" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/50 border border-gray-200/60 shadow-sm hover:bg-white transition-all cursor-pointer focus:outline-none">
                    <span class="text-xs font-bold text-gray-700">{{ Auth::user()->name }}</span>
                    <i class="fa-solid fa-circle-user text-xl text-gray-600"></i>
                </button>

                <button id="notificationBtn" class="relative p-2 bg-white rounded-xl border border-gray-200/60 shadow-sm focus:outline-none hover:bg-gray-50 transition-all cursor-pointer">
                    <i class="fa-regular fa-bell text-sm text-gray-600"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <div id="notificationMenu" class="hidden absolute right-0 top-12 bg-white rounded-3xl shadow-xl border border-gray-100 p-6 w-[340px] z-50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold text-gray-800">Pemberitahuan Sistem</h3>
                        <button class="text-[11px] font-bold text-blue-500 hover:text-blue-600">Tandai dibaca</button>
                    </div>
                    
                    <div class="space-y-4 max-h-[320px] overflow-y-auto pr-1">
                        <div class="flex items-start gap-3 p-1">
                            <div class="w-8 h-8 bg-red-50 rounded-full flex items-center justify-center text-red-500 flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-gray-800">Percobaan Akses Diblokir!</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-relaxed">Sistem CyberProtect mendeteksi dan memblokir 1 percobaan login ilegal dari perangkat tidak dikenal.</p>
                                <span class="inline-block text-[9px] font-extrabold bg-red-50 text-red-500 px-2 py-0.5 rounded mt-2 tracking-wide">CYBERPROTECT</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-1">
                            <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center text-green-500 flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-gray-800">Pembayaran QRIS Berhasil</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5 leading-relaxed">Dana pembayaran sebesar <strong class="text-gray-700 font-mono">Rp 60.500</strong> telah berhasil diterima.</p>
                                <span class="inline-block text-[9px] font-extrabold bg-green-50 text-green-600 px-2 py-0.5 rounded mt-2 tracking-wide">KASIR POS</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="profileDropdownMenu" class="hidden absolute right-12 top-12 bg-white rounded-2xl shadow-xl border border-gray-100 p-5 w-60 z-50">
                    <div class="mb-4">
                        <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <hr class="border-gray-100 my-3">
                    <div class="space-y-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-3 px-2 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-all">
                            <i class="fa-solid fa-user-gear text-sm text-gray-400"></i> Pengaturan Profil
                        </a>
                        <button type="button" class="trigger-logout-modal w-full flex items-center gap-3 px-2 py-2 text-xs font-bold text-red-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all text-left cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i> Keluar Sistem
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <div id="history-table-view" class="space-y-6 block">
            
            <div class="bg-white p-8 rounded-[32px] shadow-sm border border-gray-100/50">
                
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-2 border border-gray-200 bg-gray-50/50 px-4 py-2 rounded-full text-xs text-gray-600 font-medium">
                        <i class="fa-solid fa-bars text-gray-400"></i>
                        <span>Menampilkan data untuk: <strong class="text-gray-800">{{ Auth::user()->name }}</strong></span>
                    </div>
                    
                    <div class="relative w-80">
                        <input type="text" placeholder="Cari No. invoice" class="w-full border border-gray-200 bg-gray-50/50 rounded-full pl-10 pr-4 py-2 text-xs focus:outline-none focus:border-blue-500 text-gray-700">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-gray-400 font-bold uppercase tracking-wider text-[10px] border-b border-gray-100">
                                <th class="pb-4 px-3">Invoice ID</th>
                                <th class="pb-4 px-3">Nama Toko</th>
                                <th class="pb-4 px-3">Tanggal & Waktu</th>
                                <th class="pb-4 px-3">Metode</th>
                                <th class="pb-4 px-3">Jumlah</th>
                                <th class="pb-4 px-3">Status</th>
                                <th class="pb-4 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">

                        @foreach($transaksis as $trx)

                        <tr class="hover:bg-gray-50/60 transition-colors">

                            <td class="py-4 px-3 font-bold text-gray-900 font-mono">
                                {{ $trx->invoice_id }}
                            </td>

                            <td class="py-4 px-3 font-semibold text-gray-800">
                                {{ Auth::user()->nama_umkm }}
                            </td>

                            <td class="py-4 px-3 text-gray-500">
                                {{ $trx->created_at->format('d M Y H:i') }}
                            </td>

                            <td class="py-4 px-3">
                                {{ strtoupper($trx->metode_bayar) }}
                            </td>

                            <td class="py-4 px-3 font-bold text-gray-900 font-mono">
                                Rp {{ number_format($trx->total,0,',','.') }}
                            </td>

                            <td class="py-4 px-3">

                                @if($trx->status == 'sukses')

                                    <span class="bg-green-50 text-green-600 border border-green-100 px-3 py-0.5 rounded-full text-[10px] font-bold">
                                        Sukses
                                    </span>

                                @elseif($trx->status == 'mencurigakan')

                                    <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-0.5 rounded-full text-[10px] font-bold">
                                        Mencurigakan
                                    </span>

                                @else

                                    <span class="bg-orange-50 text-orange-600 border border-orange-100 px-3 py-0.5 rounded-full text-[10px] font-bold">
                                        Gagal
                                    </span>

                                @endif

                            </td>

                            <td class="py-4 px-3 text-center">
                                -
                            </td>

                        </tr>

                        @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100 text-gray-400 text-[11px] font-medium">
                    <p>Menampilkan 1-8 dari 100 transaksi</p>
                    <div class="flex items-center gap-1">
                        <button class="w-6 h-6 border border-gray-200 bg-white rounded flex items-center justify-center text-gray-500 hover:bg-gray-50"><i class="fa-solid fa-chevron-left text-[9px]"></i></button>
                        <button class="w-6 h-6 bg-[#010915] text-white rounded font-bold shadow-sm">1</button>
                        <button class="w-6 h-6 border border-gray-200 bg-white rounded flex items-center justify-center text-gray-500 hover:bg-gray-50">2</button>
                        <button class="w-6 h-6 border border-gray-200 bg-white rounded flex items-center justify-center text-gray-500 hover:bg-gray-50"><i class="fa-solid fa-chevron-right text-[9px]"></i></button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="bg-[#010915] text-white p-6 rounded-[24px] flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Penjualan Hari Ini</p>
                        <h4 class="text-2xl font-black mt-2 tracking-wide">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-blue-400 text-base"><i class="fa-solid fa-wallet"></i></div>
                </div>

                <div class="bg-white p-6 rounded-[24px] border border-gray-100 flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Transaksi Sukses</p>
                        <h4 class="text-2xl font-black text-gray-800 mt-2">{{ $transaksiSukses }}
    <span class="text-[11px] font-medium text-gray-400 ml-1">Transaksi Berhasil</span></h4>
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 text-base"><i class="fa-solid fa-circle-check"></i></div>
                </div>

                <div class="bg-white p-6 rounded-[24px] border border-gray-100 flex items-center justify-between shadow-sm">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Anomali Terdeteksi</p>
                        <h4 class="text-2xl font-black text-gray-800 mt-2">{{ $anomali }}<span class="text-[11px] font-medium text-red-400 ml-1">Memerlukan Verifikasi Admin</span></h4>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-500 text-base"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
            </div>
        </div>

        <div id="invoice-detail-view" class="hidden space-y-4">
            <button onclick="hideInvoiceDetail()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-bold text-xs mb-4 transition-all active:scale-95">
                <i class="fa-solid fa-chevron-left text-[9px]"></i> Kembali
            </button>

            <div class="bg-white rounded-[24px] border border-gray-100 p-8 max-w-4xl mx-auto shadow-sm">
                <div class="flex justify-between items-start border-b border-gray-100 pb-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-[#010915] text-white flex items-center justify-center rounded-xl text-lg">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h4 id="detail-store-name" class="font-bold text-gray-800 text-base leading-none">Alneyra Coffee</h4>
                            <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                                Gedung Central Business District lantai 2,<br>
                                Jakarta Selatan, DKI Jakarta 12190<br>
                                Telp: (021) 555-0192 | support@system.id
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] bg-blue-50 text-blue-600 font-black px-3 py-1 rounded-full uppercase tracking-wider">Invoice</span>
                        <p class="text-[10px] text-gray-400 font-medium mt-4">ID Transaksi</p>
                        <p id="detail-invoice-id" class="text-sm font-bold text-gray-800 font-mono mt-0.5">#TRX-882910</p>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4 bg-gray-50 rounded-2xl p-5 my-6 text-xs border border-gray-100/50">
                    <div>
                        <span class="text-gray-400 block font-bold uppercase tracking-wider text-[9px]">Tanggal & Waktu</span>
                        <span id="detail-date" class="font-bold text-gray-700 block mt-1">24 Okt 2025</span>
                        <span id="detail-time" class="text-gray-400 block font-mono text-[11px] mt-0.5">14:20:05</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold uppercase tracking-wider text-[9px]">Kasir</span>
                        <span class="font-bold text-gray-700 block mt-1">Annisa (T-04)</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold uppercase tracking-wider text-[9px]">Metode Bayar</span>
                        <span id="detail-method" class="font-black text-blue-600 block mt-1 uppercase text-[11px]">QRIS</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-bold uppercase tracking-wider text-[9px]">Status</span>
                        <span id="detail-status-badge" class="inline-block text-[10px] bg-green-500 text-white font-bold px-2.5 py-0.5 rounded mt-1 shadow-sm">Selesai</span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="text-gray-400 border-b border-gray-100 uppercase text-[10px] tracking-wider font-bold pb-3">
                                <th class="pb-3 px-1">Item Layanan / Menu</th>
                                <th class="pb-3 text-right">Harga Satuan</th>
                                <th class="pb-3 text-center">Qty</th>
                                <th class="pb-3 text-right px-1">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">

                        </tbody>                    </table>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex flex-col items-end gap-2 text-xs font-semibold">
                    <div class="flex justify-between w-64 text-gray-400">
                        <span>Subtotal:</span>
                        <span class="font-mono text-gray-700">Rp 55.000</span>
                    </div>
                    <div class="flex justify-between w-64 text-gray-400">
                        <span>Pajak (10%):</span>
                        <span class="font-mono text-gray-700">Rp 5.500</span>
                    </div>
                    <div class="flex justify-between w-64 text-base font-bold text-gray-900 mt-2 pt-2 border-t border-dashed border-gray-200">
                        <span>Total Akhir:</span>
                        <span class="font-mono">Rp 60.500</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="logout-modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-[28px] shadow-2xl w-[90%] max-w-[380px] p-8 text-center border border-gray-100 transform scale-95 opacity-0 transition-all duration-300 dynamic-modal-card">
            <h3 class="text-lg font-bold text-gray-900 tracking-wide mb-5">Konfirmasi Log Out</h3>
            <div class="flex justify-center mb-6">
                <div class="text-gray-900"><i class="fa-solid fa-arrow-right-from-bracket text-5xl"></i></div>
            </div>
            <p class="text-xs font-medium text-gray-600 leading-relaxed mb-6 px-2">Apakah Anda yakin ingin keluar dari<br><span class="font-bold text-gray-900">CyberProtect UMKM?</span></p>
            <div class="flex flex-col gap-2.5 w-full">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full bg-[#0D1F3D] hover:bg-slate-800 text-white text-[11px] font-bold py-3 rounded-full transition-all tracking-wider cursor-pointer">➔ YA, LOG OUT</button>
                </form>
                <button type="button" id="close-logout-modal-btn" class="w-full bg-[#7A7A7A] hover:bg-gray-600 text-white text-[11px] font-bold py-3 rounded-full transition-all tracking-wider cursor-pointer">↩ TIDAK, KEMBALI</button>
            </div>
        </div>
    </div>

    <script>
        // Handler untuk menampilkan/menyembunyikan detail invoice asli
        function showInvoiceDetail(id, store, date, time, method, status) {
            document.getElementById('history-table-view').classList.remove('block');
            document.getElementById('history-table-view').classList.add('hidden');
            document.getElementById('invoice-detail-view').classList.remove('hidden');
            
            document.getElementById('detail-invoice-id').innerText = id;
            document.getElementById('detail-store-name').innerText = store;
            document.getElementById('detail-date').innerText = date;
            document.getElementById('detail-time').innerText = time;
            document.getElementById('detail-method').innerText = method;
            
            const badge = document.getElementById('detail-status-badge');
            badge.innerText = status;
            if(status === 'Sukses' || status === 'Selesai') {
                badge.className = "inline-block text-[10px] bg-green-500 text-white font-bold px-2.5 py-0.5 rounded mt-1 shadow-sm";
            } else if(status === 'Tertunda') {
                badge.className = "inline-block text-[10px] bg-orange-400 text-white font-bold px-2.5 py-0.5 rounded mt-1 shadow-sm";
            } else {
                badge.className = "inline-block text-[10px] bg-red-500 text-white font-bold px-2.5 py-0.5 rounded mt-1 shadow-sm";
            }
        }

        function hideInvoiceDetail() {
            document.getElementById('invoice-detail-view').classList.add('hidden');
            document.getElementById('history-table-view').classList.remove('hidden');
            document.getElementById('history-table-view').classList.add('block');
        }

        // LOGIKA BARU: Dropdown Notifikasi Lonceng & Dropdown Profil
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationMenu = document.getElementById('notificationMenu');
        const profileDropdownBtn = document.getElementById('profileDropdownBtn');
        const profileDropdownMenu = document.getElementById('profileDropdownMenu');

        // Toggle dropdown Lonceng
        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationMenu.classList.toggle('hidden');
            profileDropdownMenu.classList.add('hidden'); // Tutup profile menu jika terbuka
        });

        // Toggle dropdown Profil
        profileDropdownBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileDropdownMenu.classList.toggle('hidden');
            notificationMenu.classList.add('hidden'); // Tutup notifikasi menu jika terbuka
        });

        // Tutup dropdown otomatis jika klik di luar area menu
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target) && !notificationMenu.contains(e.target)) {
                notificationMenu.classList.add('hidden');
            }
            if (!profileDropdownBtn.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                profileDropdownMenu.classList.add('hidden');
            }
        });

        // Logika Logout Modal (Tetap Dipertahankan)
        const logoutModal = document.getElementById('logout-modal');
        const closeLogoutBtn = document.getElementById('close-logout-modal-btn');
        
        document.querySelectorAll('.trigger-logout-modal').forEach(button => {
            button.addEventListener('click', () => {
                logoutModal.classList.remove('hidden');
                setTimeout(() => {
                    logoutModal.querySelector('.dynamic-modal-card').classList.remove('scale-95', 'opacity-0');
                    logoutModal.querySelector('.dynamic-modal-card').classList.add('scale-100', 'opacity-100');
                }, 50);
            });
        });

        closeLogoutBtn.addEventListener('click', () => {
            logoutModal.querySelector('.dynamic-modal-card').classList.remove('scale-100', 'opacity-100');
            logoutModal.querySelector('.dynamic-modal-card').classList.add('scale-95', 'opacity-0');
            setTimeout(() => logoutModal.classList.add('hidden'), 300);
        });
    </script>
</body>
</html>