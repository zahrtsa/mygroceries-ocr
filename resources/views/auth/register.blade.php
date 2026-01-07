<x-guest-layout>
    {{-- Wrapper: kasih margin kanan kiri di HP & tablet --}}
    <div class="flex justify-center items-center min-h-[75vh] px-4 sm:px-6 py-8">
        <div class="w-full max-w-2xl flex flex-col md:flex-row rounded-2xl overflow-hidden
                    border border-red-200/70 shadow-2xl bg-white">

            {{-- Kiri: Ilustrasi gambar (hanya md ke atas) --}}
            <div class="hidden md:block w-1/3 min-w-[180px] max-w-[220px]">
                <div class="h-full w-full relative overflow-hidden">
                    <img
                        src="{{ asset('img/ilus-image.png') }}"
                        alt="Ilustrasi"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>

            {{-- Kanan: Form --}}
            <div class="flex-1 flex flex-col justify-center bg-white p-5 sm:p-7 md:p-8 animate-slide-in-left">
                <h2 class="mb-3 text-xl font-extrabold text-red-700 text-left">
                    Daftar Akun
                </h2>

                <form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-3">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-red-800 mb-1">Email</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                               class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                      placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm"
                               placeholder="Alamat email"/>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama lengkap & Username --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="w-full sm:w-1/2">
                            <label for="name" class="block text-xs font-medium text-red-800 mb-1">Nama Lengkap</label>
                            <input id="name" name="name" type="text" required value="{{ old('name') }}"
                                   class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                          placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm"
                                   placeholder="Nama lengkap"/>
                            @error('name')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full sm:w-1/2">
                            <label for="username" class="block text-xs font-medium text-red-800 mb-1">Username</label>
                            <input id="username" name="username" type="text" required value="{{ old('username') }}"
                                   class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                          placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm"
                                   placeholder="Username"/>
                            @error('username')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Pendapatan & Budget --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="w-full sm:w-1/2">
                            <label for="pendapatan_bulanan" class="block text-xs font-medium text-red-800 mb-1">
                                Pendapatan Bulanan (Rp)
                            </label>
                            <input id="pendapatan_bulanan" name="pendapatan_bulanan" type="number" min="0" required
                                   value="{{ old('pendapatan_bulanan') }}"
                                   class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                          placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm"
                                   placeholder="Cth: 5000000"/>
                            @error('pendapatan_bulanan')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full sm:w-1/2">
                            <label for="budget_bulanan" class="block text-xs font-medium text-red-800 mb-1">
                                Budget Bulanan (Rp)
                            </label>
                            <input id="budget_bulanan" name="budget_bulanan" type="number" min="0" required
                                   value="{{ old('budget_bulanan') }}"
                                   class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                          placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm"
                                   placeholder="Cth: 2000000"/>
                            @error('budget_bulanan')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Password & Konfirmasi --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="w-full sm:w-1/2 relative">
                            <label for="password" class="block text-xs font-medium text-red-800 mb-1">Password</label>
                            <input id="password" name="password" type="password" required
                                   class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                          placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm pr-9"
                                   placeholder="Minimal 8 karakter"/>
                            <button type="button" id="toggle-password" tabindex="-1"
                                    class="absolute bottom-2 right-3 text-red-400 focus:outline-none"
                                    aria-label="Show password">
                                <i class="fa-solid fa-eye" id="icon-eye"></i>
                            </button>
                            @error('password')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full sm:w-1/2">
                            <label for="password_confirmation" class="block text-xs font-medium text-red-800 mb-1">
                                Konfirmasi Password
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="w-full rounded-xl border border-red-200 bg-white px-3 py-2 text-red-800
                                          placeholder:text-red-300 focus:outline-none focus:ring-2 focus:ring-red-400 text-sm"
                                   placeholder="Ulangi password"/>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full mt-2 rounded-xl bg-gradient-to-r from-red-600 to-red-400 py-2 text-white
                                   font-bold tracking-wide shadow-lg hover:from-red-700 hover:to-red-500
                                   focus:outline-none focus:ring-2 focus:ring-red-400 transition-all">
                        Daftar & Mulai Atur Keuangan
                    </button>

                    {{-- Link ke login: versi lebih simple --}}
                    <p class="mt-2 text-xs text-center text-red-700">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center justify-center px-4 py-1.5 ml-1 rounded-full
                                  border border-red-300 text-[11px] font-semibold tracking-wide
                                  text-red-700 bg-white hover:bg-red-50 hover:border-red-400
                                  focus:outline-none focus:ring-2 focus:ring-red-300/70 transition">
                            Login di sini
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes slide-in-left {
            0% { opacity: 0; transform: translateX(-60px); }
            80% { opacity: 1; transform: translateX(8px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in-left {
            animation: slide-in-left 0.9s cubic-bezier(0.4,0,0.2,1) both;
        }
    </style>

    <script>
        const eyeBtn  = document.getElementById('toggle-password');
        const pwdInput = document.getElementById('password');
        const eyeIcon = document.getElementById('icon-eye');
        if (eyeBtn && pwdInput && eyeIcon) {
            eyeBtn.addEventListener('click', function() {
                if (pwdInput.type === "password") {
                    pwdInput.type = "text";
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    pwdInput.type = "password";
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }
    </script>
</x-guest-layout>
