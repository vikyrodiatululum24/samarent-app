<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
        
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                              type="password"
                              name="password"
                              required autocomplete="current-password" />
        
                <button type="button" onclick="togglePassword()" 
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" 
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                         class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 
                                 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        
        

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif --}}

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';

            // Ganti icon jadi "mata silang" (hidden eye)
            eyeIcon.outerHTML = `
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                     class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 
                             0-8.269-2.944-9.543-7a10.06 10.06 
                             0 012.357-4.162m1.528-1.39A9.956 9.956 
                             0 0112 5c4.478 0 8.269 2.944 9.543 
                             7a10.056 10.056 0 01-4.482 5.568M15 
                             12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />
                </svg>
            `;
        } else {
            passwordInput.type = 'password';

            // Ganti icon jadi "mata biasa"
            eyeIcon.outerHTML = `
                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" 
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" 
                     class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 
                             9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            `;
        }
    }
</script>


