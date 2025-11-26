<x-guest-layout>
    @section('title', 'Konfirmasi Password')
    @section('subtitle', 'Silakan konfirmasi password Anda untuk melanjutkan')

    <!-- Display errors if any -->
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="mb-6 text-sm text-gray-600 text-center">
        {{ __('Ini adalah area aman dari aplikasi. Mohon konfirmasi password Anda sebelum melanjutkan.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input id="password"
                       type="password"
                       name="password"
                       required
                       autocomplete="current-password"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="••••••••">
            </div>
            @error('password')
            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="text-sm text-blue-600 hover:text-blue-800" href="{{ route('login') }}">
                {{ __('Kembali ke halaman login') }}
            </a>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                {{ __('Konfirmasi') }}
            </button>
        </div>
    </form>
</x-guest-layout>
