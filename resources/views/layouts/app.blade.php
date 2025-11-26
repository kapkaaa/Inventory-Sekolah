<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistem Inventaris Sekolah') }} - @yield('title', 'Dashboard')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js untuk interaktivitas -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg" x-data="{ mobileMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">
                                📚 {{ config('app.name', 'Sistem Inventaris Sekolah') }}
                            </a>
                        </div>

                        <!-- Desktop Menu -->
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Dashboard
                            </a>

                            <a href="{{ route('inventaris.index') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('inventaris.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Inventaris
                            </a>

                            <a href="{{ route('peminjaman.index') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('peminjaman.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Peminjaman
                            </a>

                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('laporan.index') }}"
                               class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('laporan.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Laporan
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- User Menu -->
                    <div class="hidden md:ml-6 md:flex md:items-center">
                        <div class="ml-3 relative" x-data="{ open: false }">
                            <div>
                                <button @click="open = !open"
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <span class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-800 rounded-full">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span class="font-medium">{{ auth()->user()->name }}</span>
                                    </span>
                                </button>
                            </div>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-xs text-gray-500">
                                        Role: <span class="font-semibold text-gray-700">{{ ucfirst(auth()->user()->role) }}</span>
                                    </div>
                                    <hr>
                                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Dashboard
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="md:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50' }} text-base font-medium">
                        Dashboard
                    </a>
                    <a href="{{ route('inventaris.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('inventaris.*') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50' }} text-base font-medium">
                        Inventaris
                    </a>
                    <a href="{{ route('peminjaman.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('peminjaman.*') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50' }} text-base font-medium">
                        Peminjaman
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('laporan.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('laporan.*') ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50' }} text-base font-medium">
                        Laporan
                    </a>
                    @endif
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-500 text-white font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}</div>
                            <div class="text-sm font-medium text-gray-500">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-6">
            @yield('content')
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