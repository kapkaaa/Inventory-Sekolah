@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Detail User</h1>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Edit
                </a>
                <a href="{{ route('users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Kembali
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informasi User</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Peran</p>
                            <p class="text-lg font-medium text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="text-lg font-medium text-gray-900">
                                @if($user->is_approved)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Menunggu
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Dibuat</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Terakhir Diupdate</p>
                            <p class="text-lg font-medium text-gray-900">{{ $user->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Profil</h2>
                    <div class="flex flex-col items-center">
                        <div class="flex-shrink-0 h-32 w-32 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <span class="text-blue-800 text-3xl font-bold">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <p class="text-lg text-gray-700">{{ $user->name }}</p>
                        <p class="text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Button -->
        @if($user->id !== auth()->id())
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Aksi</h2>
            <form method="POST" action="{{ route('users.destroy', $user) }}"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')"> 
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Hapus User
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection