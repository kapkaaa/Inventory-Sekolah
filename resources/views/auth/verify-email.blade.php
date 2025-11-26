<x-guest-layout>
    @section('title', 'Verifikasi Email')
    @section('subtitle', 'Silakan verifikasi alamat email Anda')

    @if (session('status') == 'verification-link-sent')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ __('Link verifikasi baru telah dikirim ke alamat email yang Anda berikan saat registrasi.') }}
        </div>
    @endif

    <div class="mb-6 text-sm text-gray-600 text-center">
        {{ __('Terima kasih sudah mendaftar! Sebelum memulai, apakah Anda bisa verifikasi alamat email dengan mengklik link yang kami kirimkan ke email Anda? Jika Anda tidak menerima email, kami dengan senang hati akan mengirimkan yang lain.') }}
    </div>

    <div class="grid grid-cols-1 gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                {{ __('Kirim Ulang Link Verifikasi') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
