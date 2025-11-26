@extends('layouts.auth')

@section('title', 'Login')
@section('subtitle', 'Silakan masuk untuk melanjutkan')

@section('content')
<div class="bg-white rounded-lg shadow-md p-8">
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('status'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('status') }}
    </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-6">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="email@example.com">
            </div>
        </div>

        <!-- Password -->
        <div class="mb-6">
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
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="••••••••">
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <input id="remember_me"
                       type="checkbox"
                       name="remember"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                    Ingat saya
                </label>
            </div>

            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Lupa password?
            </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
            Masuk
        </button>
    </form>

    <!-- Pending Account Note -->
    @if(session('error') && strpos(session('error'), 'persetujuan') !== false)
    <div class="mt-4 bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded text-sm">
        <p><strong>Informasi:</strong> Jika Anda baru saja mendaftar, akun Anda mungkin masih menunggu persetujuan dari admin. Silakan hubungi admin untuk informasi lebih lanjut.</p>
    </div>
    @endif

    <!-- Demo Credentials
    <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-xs text-gray-600 text-center mb-3 font-semibold">Demo Login:</p>
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="bg-blue-50 p-3 rounded-lg">
                <p class="font-semibold text-blue-800 mb-1">👨‍💼 Admin</p>
                <p class="text-gray-600">admin@sekolah.com</p>
                <p class="text-gray-600">password</p>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
                <p class="font-semibold text-green-800 mb-1">👤 User</p>
                <p class="text-gray-600">john@sekolah.com</p>
                <p class="text-gray-600">password</p>
            </div>
        </div>
    </div> -->

    <!-- Registration Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                Daftar di sini
            </a>
        </p>
    </div>
</div>
@endsection