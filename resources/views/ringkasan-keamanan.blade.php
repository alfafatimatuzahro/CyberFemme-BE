<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Keamanan & Log Aktivitas Login - CyberProtect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-stretch h-screen text-sm overflow-hidden">

    <aside class="w-64 bg-[#0B2046] text-white flex flex-col justify-between shrink-0">
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

    <main class="flex-1 flex flex-col relative">
        <header class="bg-white border-b border-gray-200 p-4 flex justify-between items-center z-10">
            <h2 class="text-2xl font-bold text-[#0B2046]">Ringkasan Keamanan & Log Aktivitas Login</h2>

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

        <div class="p-6 overflow-y-auto">
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 border rounded-xl shadow-sm">
                    <p class="text-xs text-gray-500 font-bold">TOTAL LOGIN</p>
                    <p class="text-2xl font-bold">{{ $totalLogin }}</p>
                </div>
                <div class="bg-white p-4 border rounded-xl shadow-sm">
                    <p class="text-xs text-red-500 font-bold">LOGIN GAGAL</p>
                    <p class="text-2xl font-bold text-red-600">{{ $loginGagal }}</p>
                </div>
                <div class="bg-white p-4 border rounded-xl shadow-sm">
                    <p class="text-xs text-gray-500 font-bold">SESI AKTIF</p>
                    <p class="text-2xl font-bold">{{ $sesiAktif }}</p>
                </div>
                <div class="bg-white p-4 border rounded-xl shadow-sm border-red-200">
                    <p class="text-xs text-red-500 font-bold">ANCAMAN TERDETEKSI</p>
                    <p class="text-2xl font-bold text-red-600">{{ $ancaman }}</p>
                </div>
            </div>

            <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b uppercase text-xl text-black font-bold">
                        <tr>
                            <th class="p-4">LOGIN KARYAWAN</th>
                        </tr>
                    </thead>
                    <thead class="bg-gray-50 border-b uppercase text-xs text-gray-500 font-bold">
                        <tr>
                            <th class="p-4">ID Karyawan</th>
                            <th class="p-4">Nama Karyawan</th>
                            <th class="p-4">Waktu Login</th>
                            <th class="p-4">Lokasi/IP Address</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($logs as $log)
                    <tr class="border-b">

                        <td class="p-4">
                            {{ $log->user->username ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $log->user->email ?? '-' }}
                        </td>

                        <td class="p-4">
                            {{ $log->created_at }}
                        </td>

                        <td class="p-4">
                            {{ $log->lokasi }} / {{ $log->ip_address }}
                        </td>

                        <td class="p-4">
                            {{ ucfirst($log->status) }}
                        </td>

                        <td class="p-4">
                            -
                        </td>

                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modal" class="hidden fixed inset-0 bg-black/50 flex justify-center items-center z-50">
        <div class="bg-white p-6 rounded-2xl w-96 text-center shadow-2xl">
            <h3 class="font-bold text-lg mb-2">Konfirmasi Force Logout</h3>
            <p class="text-gray-500 text-xs mb-6">Tindakan ini akan memutuskan sesi aktif karyawan secara paksa.</p>
            <div class="flex gap-2">
                <button onclick="closeModal()" class="flex-1 border py-2 rounded-lg font-bold">Batal</button>
                <button onclick="executeLogout()" class="flex-1 bg-red-700 text-white py-2 rounded-lg font-bold">Ya, Putuskan Sesi</button>
            </div>
        </div>
    </div>

    <script>
        function toggleElement(id) {
            const el = document.getElementById(id);
            if (el) {
                el.classList.toggle('hidden');
            }
        }
        let selectedRowId = null;

        function showModal(id) {
            selectedRowId = id;
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            // Jika batal, ubah status jadi aman
            document.getElementById('status-' + selectedRowId).innerText = "AMAN";
            document.getElementById('status-' + selectedRowId).className = "bg-green-100 text-green-600 px-2 py-1 rounded-full font-bold";
        }

        function executeLogout() {
            document.getElementById('modal').classList.add('hidden');
            // Logika logout
            document.getElementById('status-' + selectedRowId).innerText = "SESI DIPUTUS";
            document.getElementById('status-' + selectedRowId).className = "bg-gray-200 text-gray-600 px-2 py-1 rounded-full font-bold";
        }
    </script>
</body>
</html>