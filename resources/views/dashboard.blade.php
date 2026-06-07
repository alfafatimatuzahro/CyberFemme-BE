<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CyberProtect UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#EBEBEB] flex min-h-screen font-sans">

    <aside class="w-64 bg-[#010915] text-white flex flex-col justify-between p-6">
        <div>
            <div class="flex items-center gap-3 mb-12">
                <i class="fa-solid fa-shield-halved text-blue-500 text-2xl"></i>
                <h1 class="text-lg font-bold leading-none uppercase tracking-wider">
                    CyberProtect<br><span class="text-[10px] font-black opacity-80">UMKM</span>
                </h1>
            </div>

            <nav class="space-y-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-4 text-blue-400 bg-blue-500/10 p-3 rounded-xl font-semibold">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <a href="{{ route('pos') }}" class="flex items-center gap-4 text-gray-400 hover:text-white p-3 transition-all">
                    <i class="fa-solid fa-desktop"></i> POS
                </a>
                <a href="{{ route('history') }}" class="flex items-center gap-4 text-gray-400 hover:text-white p-3 transition-all">
                    <i class="fa-solid fa-clock-rotate-left"></i> History
                </a>
            </nav>
        </div>

        <div class="border-t border-gray-800 pt-6 flex flex-col gap-4">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 hover:bg-white/5 p-2 rounded-xl transition-all group">
                <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center group-hover:bg-gray-600 transition-colors">
                    <i class="fa-solid fa-user text-white"></i>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-bold truncate text-white group-hover:text-blue-400 transition-colors">{{ Auth::user()->username ?? 'User Name' }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email ?? 'user@email.com' }}</p>
                </div>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8">
        <div class="flex justify-end items-center gap-6 mb-4">
            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
            
            <div class="relative inline-block text-left">
                <button id="profile-menu-btn" class="flex items-center gap-2 focus:outline-none cursor-pointer">
                    <span class="text-sm font-bold text-gray-700">{{ Auth::user()->username ?? 'User Name' }}</span>
                    <i class="fa-solid fa-circle-user text-2xl text-gray-600"></i>
                </button>
                
                <div id="profile-dropdown" class="hidden absolute right-0 mt-3 w-72 bg-white rounded-[24px] shadow-2xl border border-gray-100 p-6 z-50 transform origin-top-right transition-all">
                    <div class="mb-4">
                        <h4 class="text-xl font-bold text-gray-800 leading-tight">{{ Auth::user()->name ?? 'User Name' }}</h4>
                        <p class="text-sm text-gray-400 mt-0.5">{{ Auth::user()->email ?? 'user@email.com' }}</p>
                    </div>
                    
                    <hr class="border-gray-100 my-3">
                    
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('profile') }}" class="flex items-center gap-4 py-3 text-base font-medium text-gray-600 hover:text-gray-900 transition-colors group">
                            <i class="fa-solid fa-user-gear text-xl text-gray-400 group-hover:text-gray-600 transition-colors w-6 text-center"></i> 
                            Pengaturan Profil
                        </a>
                        <button id="open-logout-modal-btn" type="button" class="w-full flex items-center gap-4 py-3 text-base font-bold text-[#EA4335] hover:text-red-700 transition-colors group text-left bg-transparent border-none cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket text-xl text-[#EA4335] w-6 text-center"></i> 
                            Keluar Sistem
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <header class="grid grid-cols-3 gap-6 mb-8 items-stretch">
            <div class="col-span-2 bg-[#00224F] text-white p-6 rounded-[16px] shadow-sm flex flex-col justify-center gap-3">
                <h2 class="text-xl font-bold tracking-wide">Halo {{ Auth::user()->username ?? 'User' }}, Selamat Bekerja !</h2>
                <div class="flex items-center gap-2 bg-white/10 w-fit px-3 py-1.5 rounded-lg border border-white/5">
                    <i class="fa-solid fa-user-clock text-xs opacity-80"></i>
                    <span class="text-xs font-medium tracking-wide opacity-90">Shift Pagi: 08:00 - 16:00</span>
                </div>
            </div>
            
            <div class="bg-[#EAEAEA] text-gray-800 p-6 rounded-[16px] flex flex-col items-center justify-center border border-gray-300/40 shadow-sm text-center">
                <i class="fa-regular fa-clock text-xl text-gray-700 mb-2"></i>
                <p id="current-time" class="text-3xl font-black tracking-tight text-gray-900 leading-none">09:10:45</p>
                <p id="current-day" class="text-[9px] uppercase tracking-widest text-gray-500 font-bold mt-2">WAKTU LOKAL TERMINAL : ...</p>
            </div>
        </header>

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[16px] shadow-sm flex items-center gap-6 border border-gray-100">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Transaksi</p>
                    <p class="text-3xl font-black text-[#010915]">{{ $totalTransaksi }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-[16px] shadow-sm flex items-center gap-6 border border-gray-100">
                <div class="w-14 h-14 bg-pink-100 text-pink-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Omzet</p>
                    <p class="text-3xl font-black text-[#010915]">Rp {{ number_format($totalOmzet,0,',','.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[24px] shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-8">
                <h3 class="font-bold text-gray-800 tracking-wide">Performa Penjualan Jam Terakhir</h3>
                <div class="flex bg-gray-100 p-1 rounded-xl gap-1">
                    <button class="px-4 py-1.5 text-xs font-bold rounded-lg bg-white shadow-sm text-gray-800">Harian</button>
                    <button class="px-4 py-1.5 text-xs font-bold text-gray-400 hover:text-gray-600">Mingguan</button>
                </div>
            </div>
            
            <div class="flex items-end justify-center gap-12 h-44 px-4 border-b border-gray-200 pb-1">
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-gray-200 rounded-t-md h-16 transition-all hover:bg-slate-300"></div>
                </div>
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-gray-200 rounded-t-md h-24 transition-all hover:bg-slate-300"></div>
                </div>
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-[#00224F] rounded-t-md h-36 transition-all hover:bg-opacity-95"></div>
                </div>
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-gray-200 rounded-t-md h-20 transition-all hover:bg-slate-300"></div>
                </div>
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-gray-200 rounded-t-md h-28 transition-all hover:bg-slate-300"></div>
                </div>
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-gray-200 rounded-t-md h-12 transition-all hover:bg-slate-300"></div>
                </div>
                <div class="flex flex-col items-center gap-2 w-12">
                    <div class="w-full bg-gray-200 rounded-t-md h-24 transition-all hover:bg-slate-300"></div>
                </div>
            </div>
            
            <div class="flex justify-center gap-12 px-4 pt-3 text-[11px] font-bold text-gray-400 tracking-wide">
                <span class="w-12 text-center">09:00</span>
                <span class="w-12 text-center">11:00</span>
                <span class="w-12 text-center">13:00</span>
                <span class="w-12 text-center">15:00</span>
                <span class="w-12 text-center">17:00</span>
                <span class="w-12 text-center">19:00</span>
                <span class="w-12 text-center text-gray-600 font-extrabold">Sekarang</span>
            </div>
        </div>
    </main>

    <div id="logout-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-[32px] shadow-2xl w-[90%] max-w-[420px] p-8 text-center border border-gray-100 transform scale-95 opacity-0 transition-all duration-300 dynamic-modal-card">
            <h3 class="text-lg font-bold text-gray-900 tracking-wide mb-6">Konfirmasi Log Out</h3>
            <div class="flex justify-center mb-6">
                <div class="w-24 h-24 rounded-2xl bg-gray-50 border-2 border-gray-100 flex items-center justify-center text-gray-800">
                    <i class="fa-solid fa-right-from-bracket text-4xl"></i>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 leading-relaxed px-4 mb-8">
                Apakah Anda yakin ingin keluar dari <br><span class="font-bold text-gray-900">CyberProtect UMKM</span>?
            </p>
            <div class="flex flex-col gap-3 w-full px-2">
                <a href="{{ route('logout') }}"
                class="block w-full bg-[#0D1F3D] hover:bg-slate-800 text-white text-[11px] font-bold py-3 rounded-full transition-all tracking-wider text-center">
                    YA, LOG OUT
                </a>
                <button type="button" id="close-logout-modal-btn" class="w-full bg-[#8A8A8A] text-white text-xs font-bold py-3.5 rounded-full hover:bg-gray-500 transition-all uppercase tracking-wide cursor-pointer">
                    TIDAK, KEMBALI
                </button>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('current-time').innerText = timeStr;
            const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
            const dayName = days[now.getDay()];
            document.getElementById('current-day').innerText = "WAKTU LOKAL TERMINAL : " + dayName;
        }
        setInterval(updateTime, 1000);
        updateTime();

        document.addEventListener("DOMContentLoaded", function () {
            const logoutModal = document.getElementById('logout-modal');
            const openModalBtn = document.getElementById('open-logout-modal-btn');
            const closeModalBtn = document.getElementById('close-logout-modal-btn');
            const modalCard = logoutModal ? logoutModal.querySelector('.dynamic-modal-card') : null;

            const profileMenuBtn = document.getElementById('profile-menu-btn');
            const profileDropdown = document.getElementById('profile-dropdown');

            if (profileMenuBtn && profileDropdown) {
                profileMenuBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function (e) {
                    if (!profileMenuBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });
            }

            if (openModalBtn && logoutModal && closeModalBtn) {
                openModalBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (profileDropdown) profileDropdown.classList.add('hidden');
                    logoutModal.classList.remove('hidden');
                    setTimeout(() => {
                        if (modalCard) {
                            modalCard.classList.remove('scale-95', 'opacity-0');
                            modalCard.classList.add('scale-100', 'opacity-100');
                        }
                    }, 10);
                });

                function closeModal() {
                    if (modalCard) {
                        modalCard.classList.remove('scale-100', 'opacity-100');
                        modalCard.classList.add('scale-95', 'opacity-0');
                    }
                    setTimeout(() => {
                        logoutModal.classList.add('hidden');
                    }, 300);
                }

                closeModalBtn.addEventListener('click', closeModal);
                logoutModal.addEventListener('click', (e) => {
                    if (e.target === logoutModal) {
                        closeModal();
                    }
                });
            }
        });
    </script>
</body>
</html>