<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User - CyberProtect UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#1E1E1E] flex flex-col justify-center items-center min-h-screen font-sans">

    <a href="{{ url('/') }}" class="text-gray-400 hover:text-white mb-4 flex items-center gap-2 text-sm transition-all">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>

    <div class="bg-white w-[400px] min-h-[550px] rounded-[24px] shadow-2xl p-8 relative flex flex-col justify-between">
        
        <div>
            <div class="flex flex-col items-center justify-center mt-6">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-[#001F5B] text-3xl"></i>
                    <h1 class="text-2xl font-bold text-[#001F5B] tracking-tight leading-none text-left">
                        CyberProtect<br><span class="text-sm font-black tracking-widest block">UMKM</span>
                    </h1>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 text-red-600 p-3 rounded-lg text-xs mt-5 font-bold border border-red-200">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.user') }}" method="POST" class="mt-8">
                @csrf
                
                <div>
                    <label class="text-xs font-bold text-gray-700 block">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email Anda"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 mt-1 text-sm focus:outline-none focus:ring-2 focus:ring-[#001F5B]/20 focus:border-[#001F5B]"
                        required
                    >
                </div>

                <div class="mt-5">
                    <label class="text-xs font-bold text-gray-700 block">
                        Kata Sandi
                    </label>
                    <div class="relative mt-1">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Masukkan kata sandi"
                            class="w-full border border-gray-300 rounded-lg pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#001F5B]/20 focus:border-[#001F5B]"
                            required
                        >
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i id="eye-icon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#001F5B] text-white py-3 rounded-lg mt-10 font-bold text-sm tracking-wide hover:bg-[#001744] transition-colors uppercase"
                >
                    Masuk
                </button>
            </form>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>