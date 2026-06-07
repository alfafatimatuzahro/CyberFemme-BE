<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CyberProtect UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen py-10">

    <div class="bg-white p-10 rounded-2xl shadow-xl w-[450px] border border-gray-100">
        <div class="flex items-center justify-center gap-2 mb-8">
            <i class="fa-solid fa-shield-halved text-3xl text-blue-800"></i>
            <h1 class="text-2xl font-bold text-blue-900 leading-tight text-left">CyberProtect<br>UMKM</h1>
        </div>

        <form id="registerForm" action="/register" method="POST" onsubmit="return validatePassword(event)">
            @csrf 

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama UMKM</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="reg_password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pr-10">
                    <button type="button" onclick="togglePassword('reg_password', 'eyeIconReg')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none">
                        <i id="eyeIconReg" class="fa-solid fa-eye"></i> </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <input type="password" id="reg_password_confirm" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pr-10">
                    <button type="button" onclick="togglePassword('reg_password_confirm', 'eyeIconConfirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none">
                        <i id="eyeIconConfirm" class="fa-solid fa-eye"></i> </button>
                </div>
                <p id="errorText" class="text-red-500 text-xs font-semibold mt-1 hidden">Konfirmasi kata sandi tidak sesuai!</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Pertanyaan Keamanan (Untuk Lupa Sandi)</label>
                <select name="security_question" required>
                    <option value="" disabled selected>Pilih Pertanyaan</option>
                    <option value="lokasi">Nama Lokasi UMKM?</option>
                    <option value="hewan">Nama Hewan Peliharaan Anda?</option>
                    <option value="kota">Kota Kelahiran Anda?</option>
                </select>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-1">Jawaban Keamanan</label>
                <input
                type="text"
                name="security_answer"
                required>
            </div>

            <button type="submit" class="w-full bg-[#0B2046] hover:bg-slate-800 text-white font-bold py-3 px-4 rounded shadow mb-4">
                DAFTAR SEKARANG
            </button>
        </form>

        <p class="text-center text-sm font-semibold text-gray-700">
            Sudah punya akun? <a href="/login" class="text-blue-800 hover:underline">Masuk</a>
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

        function validatePassword(event) {
            const password = document.getElementById('reg_password').value;
            const confirmPassword = document.getElementById('reg_password_confirm').value;
            const errorText = document.getElementById('errorText');

            if (password !== confirmPassword) {
                event.preventDefault(); // Mencegah form tersubmit
                errorText.classList.remove('hidden');
                
                alert('Peringatan: Konfirmasi kata sandi harus sama dengan kata sandi awal!');
                return false;
            }
            errorText.classList.add('hidden');
            return true;
        }
    </script>
</body>
</html>