<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CyberProtect UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-2xl shadow-xl w-[450px] border border-gray-100">
        <div class="flex items-center justify-center gap-2 mb-10">
            <i class="fa-solid fa-shield-halved text-3xl text-blue-800"></i>
            <h1 class="text-2xl font-bold text-blue-900 leading-tight text-left">CyberProtect<br>UMKM</h1>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-5 text-xs font-semibold">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf 

            <div class="mb-5">
                <label for="nama_umkm" class="block text-sm font-bold text-gray-700 mb-1">Nama UMKM</label>
                <input type="text" id="nama_umkm" name="nama_umkm" value="{{ old('nama_umkm') }}" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-8">
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pr-10">
                    
                    <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i id="eyeIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#0B2046] hover:bg-slate-800 text-white font-bold py-3 px-4 rounded shadow mb-4">
                MASUK
            </button>
        </form>

        <p class="text-center text-sm font-semibold text-gray-700">
            Belum punya akun? <a href="/register" class="text-blue-800 hover:underline">Daftar sekarang</a>
        </p>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>