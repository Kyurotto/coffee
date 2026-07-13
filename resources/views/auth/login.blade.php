<x-guest-layout>
    <div class="w-full min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">

            {{-- Brand Header --}}
            <div class="text-center space-y-4">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full overflow-hidden border-2 border-yellow-400 shadow-lg mb-2">
                    <img src="{{ asset('images/logo.png') }}?v=1.0" class="w-full h-full object-cover" alt="Coffee Shop Logo">
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Coffee Shop POS</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Please sign in to your account</p>
                </div>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                        placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300">Password</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 transition-colors" href="{{ route('password.request') }}">
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 text-yellow-500 focus:ring-yellow-400 dark:ring-offset-gray-800 transition">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">
                            {{ __('Remember me') }}
                        </span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <div class="space-y-3">
                    <button type="submit" class="w-full py-3 px-4 bg-yellow-400 hover:bg-yellow-500 text-red-950 font-bold rounded-xl shadow-lg shadow-yellow-400/25 transition-all active:scale-[0.98] flex items-center justify-center">
                        {{ __('Log in') }}
                    </button>
                    <a href="{{ route('register') }}" class="w-full py-3 px-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl border border-gray-200 dark:border-gray-700 transition-all active:scale-[0.98] flex items-center justify-center">
                        {{ __('Register') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>