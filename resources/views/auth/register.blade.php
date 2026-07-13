<x-guest-layout>
    <div class="w-full min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full space-y-8 bg-white dark:bg-gray-900 p-8 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl">

            {{-- Brand Header --}}
            <div class="text-center space-y-4">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full overflow-hidden border-2 border-yellow-400 shadow-lg mb-2">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-cover" alt="Coffee Shop Logo">
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Coffee Shop POS</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Create your account to get started</p>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Name') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                        placeholder="Your full name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Email Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                        placeholder="name@example.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent transition"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                {{-- Submit Button --}}
                <div class="space-y-3">
                    <button type="submit" class="w-full py-3 px-4 bg-yellow-400 hover:bg-yellow-500 text-red-950 font-bold rounded-xl shadow-lg shadow-yellow-400/25 transition-all active:scale-[0.98] flex items-center justify-center">
                        {{ __('Register') }}
                    </button>
                    <a href="{{ route('login') }}" class="w-full py-3 px-4 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl border border-gray-200 dark:border-gray-700 transition-all active:scale-[0.98] flex items-center justify-center">
                        {{ __('Already registered? Log in') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>