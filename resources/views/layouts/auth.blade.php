<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Inventaris Sekolah') }} - @yield('title', 'Login')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js untuk interaktivitas -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen">
        <!-- Navigation (simplified for auth pages) -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">
                                📚 {{ config('app.name', 'Sistem Inventaris Sekolah') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-md mx-auto px-4 sm:px-0">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Login')</h1>
                    <p class="mt-2 text-gray-600">@yield('subtitle', 'Silakan masuk untuk melanjutkan')</p>
                </div>

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white py-6 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
                <p>© {{ date('Y') }} {{ config('app.name', 'Sistem Inventaris Sekolah') }} - Semua Hak Dilindungi</p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>