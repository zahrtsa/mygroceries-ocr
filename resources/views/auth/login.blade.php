<x-guest-layout>
    <div id="login-card"
         class="relative bg-white/30 border border-red-600/30 shadow-2xl rounded-2xl p-0 overflow-hidden
         backdrop-blur-md transition-all duration-500 max-w-2xl mx-auto flex flex-row min-h-[430px]">
        {{-- Logo + Brand Section --}}
        <div id="logo-section"
            class="flex flex-col items-center justify-center
            bg-gradient-to-tr from-red-600/90 via-red-400/80 to-white/20
            p-8 gap-4 w-1/2 min-w-[220px] max-w-[260px] transition-all duration-700 ease-in-out relative">
            <img id="brand-logo"
                src="{{ asset('img/logo-mygroceriesround.png') }}"
                alt="Logo"
                class="w-24 h-24 rounded-xl border-4 border-white bg-white shadow-xl mb-3 opacity-0 scale-110 transition-all duration-700 ease-in-out"/>
            <div id="tagline"
                class="opacity-0 translate-y-5 transition-all duration-700 delay-150 text-left">
                <div class="text-lg font-bold leading-tight text-white drop-shadow">
                    Belanja untuk<br>
                    <span class="text-xl text-white/90">ketenangan hati</span>
                </div>
                <div class="mt-2 text-xs text-white/80 italic font-medium">
                    dengan catatan rapi melalui
                    <span class="tracking-wide font-extrabold not-italic text-white">MyGroceries</span>
                </div>
                <div class="mt-6 text-[13px] text-white/80">Silakan login &rarr;</div>
            </div>
        </div>
        {{-- Login Form --}}
        <div class="flex-1 flex items-center bg-white/60 p-8">
            <form method="POST" action="{{ route('login') }}" class="w-full space-y-5 z-10">
                @csrf
                <h2 class="mb-6 text-2xl font-extrabold text-red-700 text-center tracking-tight drop-shadow shadow-white/40">Masuk Akun</h2>
                <x-auth-session-status class="mb-4" :status="session('status')" />
                <div>
                    <x-input-label for="email" value="Email" class="text-red-700 mb-1"/>
                    <x-text-input id="email"
                        class="block mt-1 w-full bg-white/60 border border-red-300 rounded-xl text-red-900 placeholder:text-red-400 focus:ring-red-400"
                        type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="alamat@email.com"/>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
                </div>
                <div class="relative">
                    <x-input-label for="password" value="Password" class="text-red-700 mb-1"/>
                    <x-text-input id="password"
                        class="block mt-1 w-full bg-white/60 border border-red-300 rounded-xl text-red-900 placeholder:text-red-400 focus:ring-red-400 pr-10"
                        type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password"/>
                    <button type="button" id="toggle-password"
                        class="absolute bottom-2 right-4 text-red-400 hover:text-red-600 transition-colors"
                        aria-label="Show password"><i class="fa-solid fa-eye" id="icon-eye"></i>
                    </button>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600"/>
                </div>
                <div class="flex items-center gap-2">
                    <input id="remember_me" type="checkbox" class="rounded border-red-300 text-red-600 focus:ring-red-500" name="remember">
                    <label for="remember_me" class="text-sm text-red-700 select-none">Ingat saya</label>
                </div>
                <div class="flex flex-col md:flex-row items-center justify-between gap-2">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-red-400 hover:text-red-700 font-medium underline underline-offset-4 transition"
                            href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif

                    <button type="submit"
                        class="w-full md:w-auto px-7 py-2 bg-gradient-to-r from-red-600 via-red-500 to-rose-400 shadow-md rounded-xl text-white font-bold
                        hover:scale-105 hover:from-red-700 hover:to-red-600 transition-all tracking-wider border-none">
                        Log in
                    </button>
                </div>
                <p class="mt-8 text-sm text-center text-red-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                        class="ml-1 text-white font-bold bg-red-600 hover:bg-red-700 px-3 py-1 rounded-xl transition-all shadow-lg">
                        Daftar gratis →
                    </a>
                </p>
            </form>
        </div>
    </div>
    <script>
        // Animasi logo + tagline hanya fade in naik, tetap di kiri card
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.getElementById('brand-logo').classList.remove('opacity-0', 'scale-110');
                document.getElementById('brand-logo').classList.add('opacity-100', 'scale-100');
            }, 120);

            setTimeout(() => {
                document.getElementById('tagline').classList.remove('opacity-0', 'translate-y-5');
                document.getElementById('tagline').classList.add('opacity-100', 'translate-y-0');
            }, 600);
        });

        // Toggle password visibility
        const eyeBtn = document.getElementById('toggle-password');
        const pwdInput = document.getElementById('password');
        const eyeIcon = document.getElementById('icon-eye');
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
    </script>
</x-guest-layout>
