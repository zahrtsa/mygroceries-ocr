<x-guest-layout>
    {{-- Wrapper supaya ada jarak dari tepi layar --}}
    <div class="px-4 sm:px-6 py-8">
        <div id="login-card"
             class="relative bg-white border border-red-200/70 shadow-2xl rounded-2xl p-0 overflow-hidden
                    max-w-2xl mx-auto flex flex-col md:flex-row min-h-[430px]">

            {{-- Kiri: Brand + gambar (muncul hanya di md ke atas) --}}
            <div id="logo-section"
                 class="hidden md:flex relative flex-col items-center justify-center
                        w-1/2 min-w-[220px] max-w-[260px] overflow-hidden">
                {{-- Background image --}}
                <img
                    src="{{ asset('img/ilus-image2.png') }}"
                    alt="Ilustrasi"
                    class="absolute inset-0 w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

                <div class="relative z-10 flex flex-col items-center p-6 gap-4">
                    <img id="brand-logo"
                         src="{{ asset('img/logo-mygroceriesround.png') }}"
                         alt="Logo"
                         class="w-20 h-20 rounded-xl border-4 border-white bg-white shadow-xl mb-2
                                opacity-0 scale-110 transition-all duration-700 ease-in-out"/>

                    <div id="tagline"
                         class="opacity-0 translate-y-5 transition-all duration-700 delay-150 text-center">
                        <div class="text-base font-bold leading-tight text-white drop-shadow">
                            Belanja untuk<br>
                            <span class="text-lg text-white/90">ketenangan hati</span>
                        </div>
                        <div class="mt-2 text-[11px] text-white/85 italic font-medium">
                            dengan catatan rapi melalui
                            <span class="tracking-wide font-extrabold not-italic text-white">MyGroceries</span>
                        </div>
                        <div class="mt-4 text-[12px] text-white/80">
                            Silakan login untuk mulai mengatur keuangan
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: Login Form (full width di mobile & tablet) --}}
            <div class="flex-1 flex items-center bg-white p-6 sm:p-8">
                <form method="POST" action="{{ route('login') }}" class="w-full space-y-5 z-10">
                    @csrf
                    <h2 class="mb-4 text-2xl font-extrabold text-red-700 text-center tracking-tight">
                        Masuk Akun
                    </h2>

                    <x-auth-session-status class="mb-2" :status="session('status')" />

                    <div>
                        <x-input-label for="email" value="Email" class="text-red-700 mb-1"/>
                        <x-text-input id="email"
                                      class="block mt-1 w-full bg-white border border-red-200 rounded-xl
                                             text-red-900 placeholder:text-red-400 focus:ring-red-400"
                                      type="email" name="email" :value="old('email')" required autofocus
                                      autocomplete="username"
                                      placeholder="alamat@email.com"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
                    </div>

                    <div class="relative">
                        <x-input-label for="password" value="Password" class="text-red-700 mb-1"/>
                        <x-text-input id="password"
                                      class="block mt-1 w-full bg-white border border-red-200 rounded-xl
                                             text-red-900 placeholder:text-red-400 focus:ring-red-400 pr-10"
                                      type="password" name="password" required autocomplete="current-password"
                                      placeholder="Masukkan password"/>
                        <button type="button" id="toggle-password"
                                class="absolute bottom-2 right-4 text-red-400 hover:text-red-600 transition-colors"
                                aria-label="Show password">
                            <i class="fa-solid fa-eye" id="icon-eye"></i>
                        </button>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600"/>
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="remember_me" type="checkbox"
                               class="rounded border-red-300 text-red-600 focus:ring-red-500"
                               name="remember">
                        <label for="remember_me" class="text-sm text-red-700 select-none">Ingat saya</label>
                    </div>

                    <div class="flex justify-center mt-2">
                        <button type="submit"
                                class="w-full md:w-auto px-10 py-2 bg-gradient-to-r from-red-600 via-red-500 to-rose-400
                                       shadow-md rounded-xl text-white font-bold
                                       hover:scale-105 hover:from-red-700 hover:to-red-600
                                       transition-all tracking-wider border-none">
                            Log in
                        </button>
                    </div>

                    {{-- CTA Daftar gratis (versi baru) --}}
                    <p class="mt-4 text-xs text-center text-red-700">
                        Belum punya akun?
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center justify-center mt-2">
                            <button type="button"
                                class="relative inline-flex items-center justify-center px-5 py-2.5
                                       rounded-full text-xs font-semibold tracking-wide
                                       text-white shadow-md
                                       bg-gradient-to-r from-red-600 via-red-500 to-rose-500
                                       hover:from-red-700 hover:via-red-600 hover:to-rose-600
                                       focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2
                                       transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg">
                                <span class="mr-1">
                                    Daftar gratis
                                </span>
                                <i class="fa-solid fa-arrow-right text-[10px] opacity-90"></i>
                            </button>
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    {{-- Script animasi logo + toggle password --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const logo = document.getElementById('brand-logo');
                if (logo) {
                    logo.classList.remove('opacity-0', 'scale-110');
                    logo.classList.add('opacity-100', 'scale-100');
                }
            }, 120);

            setTimeout(() => {
                const tagline = document.getElementById('tagline');
                if (tagline) {
                    tagline.classList.remove('opacity-0', 'translate-y-5');
                    tagline.classList.add('opacity-100', 'translate-y-0');
                }
            }, 600);
        });

        const eyeBtn  = document.getElementById('toggle-password');
        const pwdInput = document.getElementById('password');
        const eyeIcon = document.getElementById('icon-eye');

        if (eyeBtn && pwdInput && eyeIcon) {
            eyeBtn.addEventListener('click', function () {
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
