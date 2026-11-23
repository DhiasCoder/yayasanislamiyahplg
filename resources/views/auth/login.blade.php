<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMAK YAYASAN ISLAMIYAH PALEMBANG</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f2faf4',
                            100: '#e1f4e6',
                            200: '#c3e8cd',
                            300: '#95d5a8',
                            400: '#5fba7d',
                            500: '#3a9d5d',
                            600: '#2a7e49',
                            700: '#23653c',
                            800: '#1f5132',
                            900: '#1a432b',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</head>

<body class="font-sans antialiased bg-gradient-to-b from-brand-50 via-white to-brand-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- SVG Background Pattern -->
    <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <defs>
            <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                <circle cx="30" cy="30" r="1.5" fill="#3a9d5d" opacity="0.15" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />

        <!-- Decorative Shapes -->
        <circle cx="10%" cy="20%" r="120" fill="#95d5a8" opacity="0.18" />
        <circle cx="90%" cy="80%" r="150" fill="#c3e8cd" opacity="0.30" />
        <rect x="70%" y="10%" width="200" height="200" fill="#5fba7d" opacity="0.08" transform="rotate(45 75 15)" />
        <circle cx="85%" cy="15%" r="80" fill="#95d5a8" opacity="0.15" />
        <circle cx="15%" cy="85%" r="100" fill="#c3e8cd" opacity="0.25" />
    </svg>

    <div class="relative w-full max-w-md">
        <!-- Logo/Brand Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg shadow-brand-200/60 ring-1 ring-brand-100 mb-4 overflow-hidden p-2">
                <img src="https://cdn.aceimg.com/a9zDsOTNu.png" alt="Logo Yayasan" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-800 mb-2 leading-tight">SIMAK YAYASAN ISLAMIYAH PALEMBANG</h1>
            <p class="text-brand-600">Sistem Informasi Akademik &amp; E-Learning</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-xl shadow-brand-200/50 border border-brand-100 p-6 sm:p-8">
            <h2 class="text-2xl font-bold mb-1 text-brand-900">Selamat Datang!</h2>
            <p class="text-slate-500 mb-6">Silakan masuk ke akun Anda</p>

            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-start text-sm">
                <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition duration-200 outline-none"
                            placeholder="nama@email.com"
                            required>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full pl-10 pr-12 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-brand-400 focus:border-brand-400 transition duration-200 outline-none"
                            placeholder="••••••••"
                            required>
                        <button
                            type="button"
                            onclick="togglePassword()"
                            aria-label="Tampilkan atau sembunyikan password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-brand-600 focus:outline-none">
                            <!-- Eye Icon (Show Password) -->
                            <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <!-- Eye Slash Icon (Hide Password) -->
                            <svg id="eye-slash-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-brand-500 border-slate-300 rounded focus:ring-brand-400">
                        <span class="ml-2 text-slate-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-brand-600 hover:text-brand-700 font-medium">Lupa password?</a>
                </div>

                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-brand-500 to-brand-600 text-white py-3 rounded-xl font-semibold hover:from-brand-600 hover:to-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400 focus:ring-offset-2 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-brand-500/30">
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-brand-100 text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-brand-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6l-6 6 6 6"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} SIMAK YAYASAN ISLAMIYAH PALEMBANG. All rights reserved.</p>
        </div>
    </div>

</body>

</html>