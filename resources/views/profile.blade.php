<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - CyberProtect UMKM</title>
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
                <a href="{{ route('history') }}" class="flex items-center gap-4 text-gray-400 hover:text-white p-3 text-sm font-medium transition-all">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i> History
                </a>
            </nav>
        </div>

        <div class="border-t border-gray-800 pt-6">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-2 py-2.5 rounded-xl text-left w-full bg-blue-500/10 text-blue-400 border-l-4 border-blue-500 font-bold transition-all">
                <div class="w-9 h-9 bg-gray-700 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-user text-white text-sm"></i>
                </div>
                <div class="overflow-hidden flex-1">
                    <p class="text-xs font-bold truncate text-white">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 overflow-y-auto">
        
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Profil</h2>
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-gray-700">{{ Auth::user()->name }}</span>
                <i class="fa-solid fa-circle-user text-2xl text-gray-600"></i>
                <button class="relative p-1 ml-1">
                    <i class="fa-regular fa-bell text-lg text-gray-600"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
            </div>
        </header>

        <div class="bg-white p-12 rounded-[32px] shadow-sm border border-gray-100/50 max-w-4xl mx-auto text-center">
            
            <div class="mb-8 inline-block relative">
                <div class="w-32 h-32 bg-gray-200 rounded-full flex items-center justify-center shadow-inner mx-auto border-4 border-gray-50">
                    <i class="fa-solid fa-user text-gray-400 text-5xl"></i>
                </div>
                <p class="text-xs font-bold text-gray-400 tracking-wider uppercase mt-4">Foto Profil</p>
            </div>

            <div class="max-w-xl mx-auto text-left">
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2">Nama User</label>
                        <input type="text" name="name" value="{{ Auth::user()->username }}" class="w-full border border-gray-200 bg-white rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-500 font-semibold text-gray-700 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2">Email</label>
                        <input type="email" name="email" value="{{ Auth::user()->email }}" class="w-full border border-gray-200 bg-white rounded-xl px-4 py-3 text-xs focus:outline-none focus:border-blue-500 font-semibold text-gray-700 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" value="hargapas12345" class="w-full border border-gray-200 bg-white rounded-xl pl-4 pr-10 py-3 text-xs focus:outline-none focus:border-blue-500 font-mono text-gray-600 shadow-sm">
                        </div>
                    </div>

                    <div class="flex justify-center items-center gap-4 pt-4">
                        
                        <button type="button" class="trigger-logout-modal border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-bold px-8 py-3 rounded-xl transition-all active:scale-95 shadow-sm cursor-pointer">
                            Log Out
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </main>

    <div id="logout-modal" class="hidden fixed inset-0 z-[999] flex items-center justify-center bg-black/50 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white rounded-[28px] shadow-2xl w-[90%] max-w-[380px] p-8 text-center border border-gray-100 transform scale-95 opacity-0 transition-all duration-300 dynamic-modal-card">
            
            <h3 class="text-lg font-bold text-gray-900 tracking-wide mb-5">Konfirmasi Log Out</h3>
            
            <div class="flex justify-center mb-6">
                <div class="text-gray-900">
                    <i class="fa-solid fa-arrow-right-from-bracket text-5xl"></i>
                </div>
            </div>
            
            <p class="text-xs font-medium text-gray-600 leading-relaxed mb-6 px-2">
                Apakah Anda yakin ingin keluar dari<br><span class="font-bold text-gray-900">CyberProtect UMKM?</span>
            </p>
            
            <div class="flex flex-col gap-2.5 w-full">
                <a href="{{ route('logout') }}"
                class="block w-full bg-[#0D1F3D] hover:bg-slate-800 text-white text-[11px] font-bold py-3 rounded-full transition-all tracking-wider text-center">
                ➔ YA, LOG OUT
                </a>
                
                <button type="button" id="close-logout-modal-btn" class="w-full bg-[#7A7A7A] hover:bg-gray-600 text-white text-[11px] font-bold py-3 rounded-full transition-all tracking-wider cursor-pointer">
                    ↩ TIDAK, KEMBALI
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const logoutModal = document.getElementById('logout-modal');
            const openModalButtons = document.querySelectorAll('.trigger-logout-modal');
            const closeModalBtn = document.getElementById('close-logout-modal-btn');
            const modalCard = logoutModal ? logoutModal.querySelector('.dynamic-modal-card') : null;

            openModalButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (logoutModal) {
                        logoutModal.classList.remove('hidden');
                        setTimeout(() => {
                            if (modalCard) {
                                modalCard.classList.remove('scale-95', 'opacity-0');
                                modalCard.classList.add('scale-100', 'opacity-100');
                            }
                        }, 10);
                    }
                });
            });

            function closeModal() {
                if (modalCard) {
                    modalCard.classList.remove('scale-100', 'opacity-100');
                    modalCard.classList.add('scale-95', 'opacity-0');
                }
                setTimeout(() => {
                    if (logoutModal) logoutModal.classList.add('hidden');
                }, 200);
            }

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', closeModal);
            }

            if (logoutModal) {
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