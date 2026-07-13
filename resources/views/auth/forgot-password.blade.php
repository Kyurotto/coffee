<x-guest-layout>
    <div class="w-full min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">

            {{-- Brand Header --}}
            <div class="text-center space-y-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500 rounded-2xl text-white shadow-lg shadow-amber-500/30 mb-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/>
                    </svg>
                </div>
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Coffee Admin</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Forgot your password? No problem.</p>
                </div>
            </div>

            {{-- Session Status --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">
                {{ __('Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"
                        placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Submit Button --}}
                <div class="space-y-3">
                    <button type="submit" class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/25 transition-all active:scale-[0.98] flex items-center justify-center">
                        {{ __('Email Password Reset Link') }}
                    </button>
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl border border-gray-200 dark:border-gray-700 transition-all active:scale-[0.98] flex items-center justify-center">
                        {{ __('Back to login') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>